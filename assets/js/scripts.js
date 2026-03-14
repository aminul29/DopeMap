(function ($) {
  'use strict';

  function getEditorWindow() {
    if (window.elementor && window.$e) {
      return window;
    }

    try {
      if (window.parent && window.parent !== window && window.parent.elementor && window.parent.$e) {
        return window.parent;
      }
    } catch (error) {
      // Cross-origin parent access can throw in some embed setups.
    }

    try {
      if (window.top && window.top !== window && window.top.elementor && window.top.$e) {
        return window.top;
      }
    } catch (error) {
      // Cross-origin top access can throw in some embed setups.
    }

    return null;
  }

  function isEditorMode() {
    if (window.elementorFrontend && typeof window.elementorFrontend.isEditMode === 'function') {
      return window.elementorFrontend.isEditMode();
    }

    return !!getEditorWindow();
  }

  function getElementId($widget) {
    var elementId = $widget.closest('.elementor-element').attr('data-id');

    if (elementId) {
      return elementId;
    }

    var widgetId = $widget.attr('id') || '';

    if (widgetId.indexOf('dope-map-') === 0) {
      return widgetId.replace('dope-map-', '');
    }

    return '';
  }

  function buildLocationItem(countryCode, regionName, latLng) {
    var safeCode = (countryCode || '').toUpperCase();
    var safeName = regionName || safeCode || 'Location';
    var lat = Number(latLng[0].toFixed(6));
    var lng = Number(latLng[1].toFixed(6));

    return {
      _id: 'dope_' + Math.random().toString(36).slice(2, 10),
      country_code: safeCode,
      marker_label: safeName,
      title: safeName,
      subtitle: '',
      image: {
        id: '',
        url: '',
      },
      button_text: '',
      button_url: {
        url: '',
        is_external: false,
        nofollow: false,
        custom_attributes: '',
      },
      custom_lat: lat,
      custom_lng: lng,
      marker_horizontal_orientation: 'right',
      marker_horizontal_offset: {
        size: 0,
        unit: 'px',
      },
      marker_vertical_orientation: 'top',
      marker_vertical_offset: {
        size: 0,
        unit: 'px',
      },
    };
  }

  function cloneLocations(locations) {
    if (!Array.isArray(locations)) {
      return [];
    }

    try {
      return JSON.parse(JSON.stringify(locations));
    } catch (error) {
      return locations.slice();
    }
  }

  function updateElementorLocations($widget, newLocation) {
    var editorWindow = getEditorWindow();
    var elementId = getElementId($widget);

    if (!editorWindow || !editorWindow.elementor || !elementId) {
      return false;
    }

    var container =
      typeof editorWindow.elementor.getContainer === 'function'
        ? editorWindow.elementor.getContainer(elementId)
        : null;

    if (!container || !container.settings) {
      return false;
    }

    var currentLocations = cloneLocations(container.settings.get('locations'));
    currentLocations.push(newLocation);

    if (editorWindow.$e && typeof editorWindow.$e.run === 'function') {
      editorWindow.$e.run('document/elements/settings', {
        container: container,
        settings: {
          locations: currentLocations,
        },
        options: {
          external: true,
          render: true,
          renderUI: true,
        },
      });
      return true;
    }

    if (container.settings && typeof container.settings.setExternalChange === 'function') {
      container.settings.setExternalChange({
        locations: currentLocations,
      });
      return true;
    }

    if (container.settings && typeof container.settings.set === 'function') {
      container.settings.set('locations', currentLocations);
      return true;
    }

    return false;
  }

  function getRegionName(mapObject, countryCode) {
    if (!mapObject || !mapObject.mapData || !mapObject.mapData.paths || !mapObject.mapData.paths[countryCode]) {
      return countryCode;
    }

    return mapObject.mapData.paths[countryCode].name || countryCode;
  }

  function getClickLatLng(event, $canvas, mapObject, countryCode) {
    if (!event || !$canvas || !mapObject || !mapObject.pointToLatLng) {
      return getCountryCenterLatLng(mapObject, countryCode);
    }

    var offset = $canvas.offset();

    if (!offset) {
      return getCountryCenterLatLng(mapObject, countryCode);
    }

    var x = event.pageX - offset.left;
    var y = event.pageY - offset.top;
    var converted = mapObject.pointToLatLng(x, y);

    if (!converted) {
      return getCountryCenterLatLng(mapObject, countryCode);
    }

    if (Array.isArray(converted)) {
      return converted;
    }

    if (Number.isFinite(converted.lat) && Number.isFinite(converted.lng)) {
      return [converted.lat, converted.lng];
    }

    return getCountryCenterLatLng(mapObject, countryCode);
  }

  function getMarkerOffsets(marker) {
    var horizontalOffset =
      marker && marker.marker_horizontal_offset && typeof marker.marker_horizontal_offset === 'object'
        ? marker.marker_horizontal_offset.size
        : marker && marker.offsetX;
    var verticalOffset =
      marker && marker.marker_vertical_offset && typeof marker.marker_vertical_offset === 'object'
        ? marker.marker_vertical_offset.size
        : marker && marker.offsetY;

    horizontalOffset = getMarkerOffsetValue(horizontalOffset);
    verticalOffset = getMarkerOffsetValue(verticalOffset);

    if ((marker && marker.marker_horizontal_orientation) === 'left') {
      horizontalOffset *= -1;
    }

    if ((marker && marker.marker_vertical_orientation) === 'top') {
      verticalOffset *= -1;
    }

    return {
      x: horizontalOffset,
      y: verticalOffset,
    };
  }

  function addEditorMarker(mapObject, location, markerColor, markerIconSize) {
    if (!mapObject || typeof mapObject.addMarker !== 'function' || !validLatLng([location.custom_lat, location.custom_lng])) {
      return;
    }

    var nextKey = 'dope_runtime_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
    var offsets = getMarkerOffsets(location);

    mapObject.addMarker(nextKey, {
      latLng: [location.custom_lat, location.custom_lng],
      name: location.marker_label || location.country_code || 'Location',
      style: getMarkerVisualStyle(markerColor, markerIconSize, offsets.x, offsets.y),
      _dope: {
        countryCode: location.country_code,
        name: location.marker_label,
        title: location.title,
        subtitle: location.subtitle,
        imageUrl: '',
        buttonText: '',
        buttonUrl: '',
        isExternal: false,
      },
    });
  }

  function parseConfig($widget) {
    var raw = $widget.attr('data-map-config');

    if (!raw) {
      return {};
    }

    try {
      return JSON.parse(raw);
    } catch (error) {
      return {};
    }
  }

  function validLatLng(latLng) {
    return (
      Array.isArray(latLng) &&
      latLng.length === 2 &&
      Number.isFinite(latLng[0]) &&
      Number.isFinite(latLng[1])
    );
  }

  function getMarkerIconSize(markerIconSize) {
    var parsed = Number(markerIconSize);

    if (!Number.isFinite(parsed)) {
      return 16;
    }

    return Math.max(12, Math.min(96, Math.round(parsed)));
  }

  function getMarkerIconDataUrl(color, markerIconSize) {
    var safeColor = color || '#FFFFFF';
    var safeSize = getMarkerIconSize(markerIconSize);
    var svg =
      '<svg xmlns="http://www.w3.org/2000/svg" width="' +
      safeSize +
      '" height="' +
      safeSize +
      '" viewBox="0 0 24 24">' +
      '<path fill="' +
      safeColor +
      '" d="M12 2C8.13 2 5 5.13 5 9c0 5.06 6.18 11.97 6.44 12.26a.75.75 0 0 0 1.12 0C12.82 20.97 19 14.06 19 9c0-3.87-3.13-7-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/>' +
      '</svg>';

    return 'data:image/svg+xml;utf8,' + encodeURIComponent(svg);
  }

  function getMarkerOffsetValue(offset) {
    var parsed = Number(offset);

    if (!Number.isFinite(parsed)) {
      return 0;
    }

    return Math.max(-120, Math.min(120, Math.round(parsed)));
  }

  function getMarkerVisualStyle(markerColor, markerIconSize, markerOffsetX, markerOffsetY) {
    var safeSize = getMarkerIconSize(markerIconSize);
    var safeOffsetX = getMarkerOffsetValue(markerOffsetX);
    var safeOffsetY = getMarkerOffsetValue(markerOffsetY);

    return {
      image: {
        url: getMarkerIconDataUrl(markerColor, safeSize),
        offset: [safeOffsetX, -Math.round(safeSize / 2) + safeOffsetY],
      },
    };
  }

  function getCountryCenterLatLng(mapObject, countryCode) {
    var code = (countryCode || '').toUpperCase();

    if (!code || !mapObject || !mapObject.regions || !mapObject.regions[code]) {
      return null;
    }

    var region = mapObject.regions[code];

    if (!region.element || !region.element.shape || !region.element.shape.getBBox) {
      return null;
    }

    var bbox = region.element.shape.getBBox();

    if (!bbox || !mapObject.pointToLatLng) {
      return null;
    }

    return mapObject.pointToLatLng(bbox.x + bbox.width / 2, bbox.y + bbox.height / 2);
  }

  function normalizeMarkers(rawMarkers, mapObject, markerColor, markerIconSize) {
    var prepared = [];

    (rawMarkers || []).forEach(function (marker) {
      var latLng = validLatLng(marker.latLng)
        ? [Number(marker.latLng[0]), Number(marker.latLng[1])]
        : getCountryCenterLatLng(mapObject, marker.countryCode);
      var offsets = getMarkerOffsets(marker);

      if (!validLatLng(latLng)) {
        return;
      }

      prepared.push({
        latLng: latLng,
        name: marker.name || marker.countryCode || 'Location',
        style: getMarkerVisualStyle(markerColor, markerIconSize, offsets.x, offsets.y),
        offsetX: offsets.x,
        offsetY: offsets.y,
        _dope: marker,
      });
    });

    return prepared;
  }

  function clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
  }

  function renderMarkerLabels($stage, mapObject, markers, markerIconSize) {
    var $labels = $stage.find('.dope-map-marker-labels');
    var safeMarkerIconSize = getMarkerIconSize(markerIconSize);

    if (!$labels.length) {
      $labels = $('<div class="dope-map-marker-labels" aria-hidden="true"></div>').appendTo($stage);
    }

    $labels.empty();

    if (!mapObject || !Array.isArray(markers) || !markers.length || typeof mapObject.latLngToPoint !== 'function') {
      return;
    }

    markers.forEach(function (marker) {
      if (!marker || !validLatLng(marker.latLng) || !marker.name) {
        return;
      }

      var point = mapObject.latLngToPoint(marker.latLng[0], marker.latLng[1]);

      if (!point) {
        return;
      }

      $('<div class="dope-map-marker-label"></div>')
        .text(marker.name)
        .css({
          left: point.x + getMarkerOffsetValue(marker.offsetX) + Math.max(10, safeMarkerIconSize * 0.55) + 'px',
          top: point.y + getMarkerOffsetValue(marker.offsetY) - Math.round(safeMarkerIconSize * 0.52) + 'px',
        })
        .appendTo($labels);
    });
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function setPopupContent($popup, popupData) {
    var $title = $popup.find('.dope-map-popup__title');
    var $subtitle = $popup.find('.dope-map-popup__subtitle');
    var $imageWrap = $popup.find('.dope-map-popup__image-wrap');
    var $image = $popup.find('.dope-map-popup__image');
    var $button = $popup.find('.dope-map-popup__button');
    var mode = popupData && popupData.mode ? popupData.mode : 'location';

    if (mode === 'info-table') {
      $title.text(popupData.title || '');
      if (popupData.popupContent) {
        $subtitle.html(popupData.popupContent).show();
      } else {
        $subtitle.hide().empty();
      }
      $image.attr('src', '');
      $image.attr('alt', '');
      $imageWrap.removeClass('is-visible');
      $button
        .text('')
        .attr('href', '#')
        .removeAttr('target')
        .removeAttr('rel')
        .removeClass('is-visible');

      return;
    }

    $title.text(popupData.title || popupData.name || popupData.countryCode || '');

    if (popupData.subtitle) {
      $subtitle.html(popupData.subtitle).show();
    } else {
      $subtitle.hide().empty();
    }

    if (popupData.imageUrl) {
      $image.attr('src', popupData.imageUrl);
      $image.attr('alt', popupData.title || popupData.name || '');
      $imageWrap.addClass('is-visible');
    } else {
      $image.attr('src', '');
      $image.attr('alt', '');
      $imageWrap.removeClass('is-visible');
    }

    if (popupData.buttonText && popupData.buttonUrl) {
      $button.text(popupData.buttonText).attr('href', popupData.buttonUrl);

      if (popupData.isExternal) {
        $button.attr('target', '_blank').attr('rel', 'noopener noreferrer');
      } else {
        $button.removeAttr('target').removeAttr('rel');
      }

      $button.addClass('is-visible');
    } else {
      $button
        .text('')
        .attr('href', '#')
        .removeAttr('target')
        .removeAttr('rel')
        .removeClass('is-visible');
    }
  }

  function openPopup($widget, $popup) {
    var $backdrop = $widget.find('.dope-map-popup-backdrop');

    $backdrop.prop('hidden', false);
    $popup.prop('hidden', false);
  }

  function closePopup($widget, $popup) {
    var $backdrop = $widget.find('.dope-map-popup-backdrop');

    $backdrop.prop('hidden', true);
    $popup.prop('hidden', true);
  }

  function initWidget($scope) {
    var $widget = $scope.hasClass('dope-map-widget')
      ? $scope
      : $scope.find('.dope-map-widget').first();

    if (!$widget.length || $widget.data('dopemap-initialized')) {
      return;
    }

    var config = parseConfig($widget);

    if (!$.fn.vectorMap) {
      return;
    }

    var $canvas = $widget.find('.dope-map-canvas');
    var $stage = $widget.find('.dope-map-stage');
    var $popup = $widget.find('.dope-map-popup');
    var $infoTable = $widget.find('.dope-map-info-table');
    var styles = config.styles || {};

    $canvas.vectorMap({
      map: 'world_mill_en',
      backgroundColor: styles.backgroundColor || '#0653A6',
      zoomOnScroll: false,
      normalizeFunction: 'polynomial',
      hoverOpacity: 0.92,
      markerStyle: {
        initial: {
          fill: styles.markerColor || '#FFFFFF',
          stroke: '#0F172A',
          'stroke-width': 1,
          r: 4,
        },
        hover: {
          fill: styles.markerHoverColor || '#B3D4FF',
          stroke: '#0F172A',
          'stroke-width': 1,
          r: 5,
        },
      },
      regionStyle: {
        initial: {
          fill: styles.regionDefault || '#040608',
          'fill-opacity': 1,
          stroke: '#0A1020',
          'stroke-width': 0.6,
          'stroke-opacity': 1,
        },
        hover: {
          fill: styles.regionHover || '#13263A',
          'fill-opacity': 1,
          cursor: 'pointer',
        },
      },
      markers: [],
      onMarkerClick: function (event, index) {
        var mapObject = $canvas.vectorMap('get', 'mapObject');
        var markerItem = mapObject && mapObject.markers[index] ? mapObject.markers[index] : null;

        if (!markerItem || !markerItem.config || !markerItem.config._dope) {
          return;
        }

        event.preventDefault();
        setPopupContent($popup, markerItem.config._dope);
        openPopup($widget, $popup);
      },
      onRegionClick: function (event, countryCode) {
        var mapObject = $canvas.vectorMap('get', 'mapObject');
        var latLng = getClickLatLng(event, $canvas, mapObject, countryCode);

        if (!validLatLng(latLng)) {
          return;
        }

        var regionName = getRegionName(mapObject, countryCode);
        var newLocation = buildLocationItem(countryCode, regionName, latLng);

        addEditorMarker(
          mapObject,
          newLocation,
          styles.markerColor || '#FFFFFF',
          styles.markerIconSize || 16
        );

        // Persist only when running inside Elementor editor.
        if (isEditorMode()) {
          updateElementorLocations($widget, newLocation);
        }
      },
    });

    var mapObject = $canvas.vectorMap('get', 'mapObject');
    var markers = normalizeMarkers(
      config.markers || [],
      mapObject,
      styles.markerColor || '#FFFFFF',
      styles.markerIconSize || 16
    );

    if (markers.length && mapObject && typeof mapObject.addMarkers === 'function') {
      mapObject.addMarkers(markers);
    }

    renderMarkerLabels($stage, mapObject, markers, styles.markerIconSize || 16);

    $canvas.on('viewportChange.jvectormap', function () {
      renderMarkerLabels($stage, mapObject, markers, styles.markerIconSize || 16);
    });

    $popup.find('.dope-map-popup__close').on('click', function () {
      closePopup($widget, $popup);
    });

    $widget.find('.dope-map-popup-backdrop').on('click', function () {
      closePopup($widget, $popup);
    });

    $infoTable.on('click', '.dope-map-info-table__trigger', function (event) {
      var index = Number($(event.currentTarget).attr('data-info-index'));
      var infoRows = Array.isArray(config.infoTable) ? config.infoTable : [];
      var infoRow = infoRows[index];

      if (!infoRow) {
        return;
      }

      event.preventDefault();

      setPopupContent($popup, {
        mode: 'info-table',
        title: infoRow.title || '',
        popupContent: infoRow.popupContent || '',
      });

      openPopup($widget, $popup);
    });

    $(document).on('keydown.dopemap-' + ($widget.attr('id') || ''), function (event) {
      if (event.key === 'Escape') {
        closePopup($widget, $popup);
      }
    });

    $widget.data('dopemap-initialized', true);
  }

  $(window).on('elementor/frontend/init', function () {
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
      window.elementorFrontend.hooks.addAction(
        'frontend/element_ready/dope_world_map.default',
        initWidget
      );
    }
  });

  $(function () {
    $('.dope-map-widget').each(function () {
      initWidget($(this));
    });
  });
})(jQuery);
