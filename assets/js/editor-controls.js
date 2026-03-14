(function ($) {
  'use strict';

  var strings = window.DopeMapEditorControls || {};

  function getString(key, fallback) {
    return strings[key] || fallback;
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function normalizeRows(value) {
    var rows = value;

    if (typeof rows === 'string') {
      try {
        rows = JSON.parse(rows);
      } catch (error) {
        rows = [];
      }
    }

    if (!Array.isArray(rows)) {
      return [];
    }

    return rows.map(function (row) {
      var link = row && typeof row.popup_link === 'object' && row.popup_link ? row.popup_link : {};

      return {
        popup_title: row && row.popup_title ? String(row.popup_title) : '',
        popup_link: {
          url: link.url ? String(link.url) : '',
          is_external: !!link.is_external,
          nofollow: !!link.nofollow,
        },
      };
    });
  }

  function getDefaultRow() {
    return {
      popup_title: '',
      popup_link: {
        url: '',
        is_external: false,
        nofollow: false,
      },
    };
  }

  var PopupRowsControl = elementor.modules.controls.BaseData.extend({
    ui: function () {
      return {
        input: '.dopemap-nested-repeater__input',
        rows: '.dopemap-nested-repeater__rows',
        add: '.dopemap-nested-repeater__add',
      };
    },

    events: function () {
      return {
        'click @ui.add': 'onAddRow',
        'click .dopemap-nested-repeater__action--remove': 'onRemoveRow',
        'click .dopemap-nested-repeater__action--duplicate': 'onDuplicateRow',
        'click .dopemap-nested-repeater__action--up': 'onMoveUp',
        'click .dopemap-nested-repeater__action--down': 'onMoveDown',
        'input .dopemap-nested-repeater__field': 'onFieldChange',
        'change .dopemap-nested-repeater__field': 'onFieldChange',
      };
    },

    onReady: function () {
      this.syncInputFromValue();
      this.renderRows();
    },

    getControlValueNormalized: function () {
      if (typeof this.getControlValue === 'function') {
        return normalizeRows(this.getControlValue());
      }

      return normalizeRows(this.ui.input.val());
    },

    syncInputFromValue: function () {
      this.ui.input.val(JSON.stringify(this.getControlValueNormalized()));
    },

    getRows: function () {
      return this.getControlValueNormalized();
    },

    saveRows: function (rows) {
      var serialized = JSON.stringify(normalizeRows(rows));

      if (typeof this.setValue === 'function') {
        this.setValue(serialized);
      }

      this.ui.input.val(serialized).trigger('input').trigger('change');
    },

    persistRowsFromDom: function () {
      var rows = [];

      this.ui.rows.find('.dopemap-nested-repeater__row').each(function () {
        var $row = $(this);

        rows.push({
          popup_title: $row.find('[data-field="popup_title"]').val() || '',
          popup_link: {
            url: $row.find('[data-field="popup_link_url"]').val() || '',
            is_external: $row.find('[data-field="popup_link_external"]').is(':checked'),
            nofollow: $row.find('[data-field="popup_link_nofollow"]').is(':checked'),
          },
        });
      });

      this.saveRows(rows);
    },

    buildRowHtml: function (row, index, total) {
      var titleLabel = escapeHtml(getString('titleLabel', 'Title'));
      var urlLabel = escapeHtml(getString('urlLabel', 'Link URL'));
      var externalLabel = escapeHtml(getString('externalLabel', 'Open in new tab'));
      var nofollowLabel = escapeHtml(getString('nofollowLabel', 'Add nofollow'));
      var rowLabel = escapeHtml(getString('rowLabel', 'Popup Row'));
      var duplicateLabel = escapeHtml(getString('duplicateRow', 'Duplicate'));
      var removeLabel = escapeHtml(getString('removeRow', 'Remove'));
      var moveUpLabel = escapeHtml(getString('moveUp', 'Up'));
      var moveDownLabel = escapeHtml(getString('moveDown', 'Down'));
      var title = escapeHtml(row.popup_title || '');
      var url = escapeHtml((row.popup_link && row.popup_link.url) || '');
      var disableUp = index === 0 ? ' disabled' : '';
      var disableDown = index === total - 1 ? ' disabled' : '';
      var checkedExternal =
        row.popup_link && row.popup_link.is_external ? ' checked="checked"' : '';
      var checkedNofollow =
        row.popup_link && row.popup_link.nofollow ? ' checked="checked"' : '';

      return (
        '<div class="dopemap-nested-repeater__row" data-index="' +
        index +
        '">' +
        '<div class="dopemap-nested-repeater__row-header">' +
        '<span class="dopemap-nested-repeater__row-title">' +
        rowLabel +
        ' ' +
        (index + 1) +
        '</span>' +
        '<div class="dopemap-nested-repeater__actions">' +
        '<button type="button" class="elementor-button elementor-button-default dopemap-nested-repeater__action dopemap-nested-repeater__action--up"' +
        disableUp +
        '>' +
        moveUpLabel +
        '</button>' +
        '<button type="button" class="elementor-button elementor-button-default dopemap-nested-repeater__action dopemap-nested-repeater__action--down"' +
        disableDown +
        '>' +
        moveDownLabel +
        '</button>' +
        '<button type="button" class="elementor-button elementor-button-default dopemap-nested-repeater__action dopemap-nested-repeater__action--duplicate">' +
        duplicateLabel +
        '</button>' +
        '<button type="button" class="elementor-button elementor-button-default dopemap-nested-repeater__action dopemap-nested-repeater__action--remove">' +
        removeLabel +
        '</button>' +
        '</div>' +
        '</div>' +
        '<label class="dopemap-nested-repeater__label">' +
        titleLabel +
        '<input type="text" class="dopemap-nested-repeater__field" data-field="popup_title" value="' +
        title +
        '">' +
        '</label>' +
        '<label class="dopemap-nested-repeater__label">' +
        urlLabel +
        '<input type="url" class="dopemap-nested-repeater__field" data-field="popup_link_url" value="' +
        url +
        '">' +
        '</label>' +
        '<label class="dopemap-nested-repeater__checkbox">' +
        '<input type="checkbox" class="dopemap-nested-repeater__field" data-field="popup_link_external"' +
        checkedExternal +
        '>' +
        '<span>' +
        externalLabel +
        '</span>' +
        '</label>' +
        '<label class="dopemap-nested-repeater__checkbox">' +
        '<input type="checkbox" class="dopemap-nested-repeater__field" data-field="popup_link_nofollow"' +
        checkedNofollow +
        '>' +
        '<span>' +
        nofollowLabel +
        '</span>' +
        '</label>' +
        '</div>'
      );
    },

    renderRows: function () {
      var rows = this.getRows();
      var html = '';

      if (!rows.length) {
        html =
          '<div class="dopemap-nested-repeater__empty">' +
          escapeHtml(getString('emptyState', 'No popup rows added yet.')) +
          '</div>';
      } else {
        rows.forEach(
          function (row, index) {
            html += this.buildRowHtml(row, index, rows.length);
          }.bind(this)
        );
      }

      this.ui.rows.html(html);
      this.syncInputFromValue();
    },

    onAddRow: function (event) {
      event.preventDefault();

      var rows = this.getRows();

      rows.push(getDefaultRow());
      this.saveRows(rows);
      this.renderRows();
    },

    onRemoveRow: function (event) {
      event.preventDefault();

      var index = Number($(event.currentTarget).closest('.dopemap-nested-repeater__row').data('index'));
      var rows = this.getRows();

      rows.splice(index, 1);
      this.saveRows(rows);
      this.renderRows();
    },

    onDuplicateRow: function (event) {
      event.preventDefault();

      var index = Number($(event.currentTarget).closest('.dopemap-nested-repeater__row').data('index'));
      var rows = this.getRows();
      var duplicate = rows[index] ? normalizeRows([rows[index]])[0] : getDefaultRow();

      rows.splice(index + 1, 0, duplicate);
      this.saveRows(rows);
      this.renderRows();
    },

    onMoveUp: function (event) {
      event.preventDefault();

      var index = Number($(event.currentTarget).closest('.dopemap-nested-repeater__row').data('index'));

      if (index <= 0) {
        return;
      }

      var rows = this.getRows();
      var current = rows[index];

      rows[index] = rows[index - 1];
      rows[index - 1] = current;

      this.saveRows(rows);
      this.renderRows();
    },

    onMoveDown: function (event) {
      event.preventDefault();

      var index = Number($(event.currentTarget).closest('.dopemap-nested-repeater__row').data('index'));
      var rows = this.getRows();

      if (index < 0 || index >= rows.length - 1) {
        return;
      }

      var current = rows[index];

      rows[index] = rows[index + 1];
      rows[index + 1] = current;

      this.saveRows(rows);
      this.renderRows();
    },

    onFieldChange: function () {
      this.persistRowsFromDom();
    },
  });

  function registerControlView() {
    if (window.elementor && typeof window.elementor.addControlView === 'function') {
      window.elementor.addControlView('dopemap_nested_repeater', PopupRowsControl);
    }
  }

  $(window).on('elementor:init', registerControlView);
  registerControlView();
})(jQuery);
