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

  function addEditorMarker(mapObject, location, markerColor) {
    if (!mapObject || typeof mapObject.addMarker !== 'function' || !validLatLng([location.custom_lat, location.custom_lng])) {
      return;
    }

    var nextKey = 'dope_runtime_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

    mapObject.addMarker(nextKey, {
      latLng: [location.custom_lat, location.custom_lng],
      name: location.marker_label || location.country_code || 'Location',
      style: getMarkerVisualStyle(markerColor),
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

  function getMarkerIconDataUrl(color) {
    var safeColor = color || '#FFFFFF';
    var svg =
      '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24">' +
      '<path fill="' +
      safeColor +
      '" d="M12 2C8.13 2 5 5.13 5 9c0 5.06 6.18 11.97 6.44 12.26a.75.75 0 0 0 1.12 0C12.82 20.97 19 14.06 19 9c0-3.87-3.13-7-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/>' +
      '</svg>';

    return 'data:image/svg+xml;utf8,' + encodeURIComponent(svg);
  }

  function getMarkerVisualStyle(markerColor) {
    return {
      image: {
        url: getMarkerIconDataUrl(markerColor),
        offset: [0, -13],
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

  function normalizeMarkers(rawMarkers, mapObject, markerColor) {
    var prepared = [];

    (rawMarkers || []).forEach(function (marker) {
      var latLng = validLatLng(marker.latLng)
        ? [Number(marker.latLng[0]), Number(marker.latLng[1])]
        : getCountryCenterLatLng(mapObject, marker.countryCode);

      if (!validLatLng(latLng)) {
        return;
      }

      prepared.push({
        latLng: latLng,
        name: marker.name || marker.countryCode || 'Location',
        style: getMarkerVisualStyle(markerColor),
        _dope: marker,
      });
    });

    return prepared;
  }

  function clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
  }

  function setPopupContent($popup, markerData) {
    var $title = $popup.find('.dope-map-popup__title');
    var $subtitle = $popup.find('.dope-map-popup__subtitle');
    var $imageWrap = $popup.find('.dope-map-popup__image-wrap');
    var $image = $popup.find('.dope-map-popup__image');
    var $button = $popup.find('.dope-map-popup__button');

    $title.text(markerData.title || markerData.name || markerData.countryCode || '');

    if (markerData.subtitle) {
      $subtitle.text(markerData.subtitle).show();
    } else {
      $subtitle.hide();
    }

    if (markerData.imageUrl) {
      $image.attr('src', markerData.imageUrl);
      $image.attr('alt', markerData.title || markerData.name || '');
      $imageWrap.addClass('is-visible');
    } else {
      $image.attr('src', '');
      $image.attr('alt', '');
      $imageWrap.removeClass('is-visible');
    }

    if (markerData.buttonText && markerData.buttonUrl) {
      $button.text(markerData.buttonText).attr('href', markerData.buttonUrl);

      if (markerData.isExternal) {
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

  function placePopup($widget, $popup, mapObject, markerItem) {
    if (!mapObject || !markerItem || !validLatLng(markerItem.latLng) || !mapObject.latLngToPoint) {
      return;
    }

    var point = mapObject.latLngToPoint(markerItem.latLng[0], markerItem.latLng[1]);

    if (!point) {
      return;
    }

    $popup.prop('hidden', false);

    var widgetWidth = $widget.outerWidth();
    var widgetHeight = $widget.outerHeight();
    var popupWidth = $popup.outerWidth();
    var popupHeight = $popup.outerHeight();

    var left = point.x + 12;
    var top = point.y - popupHeight - 12;

    left = clamp(left, 8, Math.max(8, widgetWidth - popupWidth - 8));

    if (top < 8) {
      top = clamp(point.y + 12, 8, Math.max(8, widgetHeight - popupHeight - 8));
    }

    $popup.css({ left: left + 'px', top: top + 'px' });
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
    var $popup = $widget.find('.dope-map-popup');
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
      onMarkerTipShow: function (event, tip, index) {
        tip.hide();

        var mapObject = $canvas.vectorMap('get', 'mapObject');
        var markerItem = mapObject && mapObject.markers[index] ? mapObject.markers[index] : null;

        if (!markerItem || !markerItem.config || !markerItem.config._dope) {
          return;
        }

        setPopupContent($popup, markerItem.config._dope);
        placePopup($widget, $popup, mapObject, markerItem.config);
      },
      onMarkerClick: function (event, index) {
        var mapObject = $canvas.vectorMap('get', 'mapObject');
        var markerItem = mapObject && mapObject.markers[index] ? mapObject.markers[index] : null;

        if (!markerItem || !markerItem.config || !markerItem.config._dope) {
          return;
        }

        event.preventDefault();
        setPopupContent($popup, markerItem.config._dope);
        placePopup($widget, $popup, mapObject, markerItem.config);
      },
      onRegionClick: function (event, countryCode) {
        var mapObject = $canvas.vectorMap('get', 'mapObject');
        var latLng = getClickLatLng(event, $canvas, mapObject, countryCode);

        if (!validLatLng(latLng)) {
          return;
        }

        var regionName = getRegionName(mapObject, countryCode);
        var newLocation = buildLocationItem(countryCode, regionName, latLng);

        addEditorMarker(mapObject, newLocation, styles.markerColor || '#FFFFFF');

        // Persist only when running inside Elementor editor.
        if (isEditorMode()) {
          updateElementorLocations($widget, newLocation);
        }
      },
    });

    var mapObject = $canvas.vectorMap('get', 'mapObject');
    var markers = normalizeMarkers(config.markers || [], mapObject, styles.markerColor || '#FFFFFF');

    if (markers.length && mapObject && typeof mapObject.addMarkers === 'function') {
      mapObject.addMarkers(markers);
    }

    $widget.on('mouseleave', function () {
      $popup.prop('hidden', true);
    });

    $popup.find('.dope-map-popup__close').on('click', function () {
      $popup.prop('hidden', true);
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
