/**
 * Global Rich Dropdown Controller
 * Handles toggling, selection, visual badge updates, and native change events.
 */
(function (global) {
  'use strict';

  var RichDropdown = {
    toggle: function (id, event) {
      if (event) {
        event.stopPropagation();
      }
      var wrapper = document.getElementById('rich_dropdown_' + id);
      if (!wrapper) return;

      var isOpen = wrapper.classList.contains('is-open');
      RichDropdown.closeAll();

      if (!isOpen) {
        wrapper.classList.add('is-open');
        var trigger = document.getElementById('trigger_' + id);
        if (trigger) trigger.setAttribute('aria-expanded', 'true');
      }
    },

    select: function (id, value, label, dot, icon, onChangeCallback, event) {
      if (event) {
        event.stopPropagation();
      }

      var hiddenInput = document.getElementById(id);
      var labelSpan = document.getElementById('label_' + id);
      var trigger = document.getElementById('trigger_' + id);
      var wrapper = document.getElementById('rich_dropdown_' + id);
      if (!wrapper) return;

      var oldValue = hiddenInput ? hiddenInput.value : null;

      // Update hidden input value
      if (hiddenInput) {
        hiddenInput.value = value;
      }

      // Update trigger text
      if (labelSpan) {
        labelSpan.textContent = label;
      }

      // Update visual dot / icon in trigger
      var labelGroup = trigger ? trigger.querySelector('.trigger-label-group') : null;
      if (labelGroup) {
        var currentDot = labelGroup.querySelector('.trigger-dot');
        var currentIcon = labelGroup.querySelector('.trigger-icon');
        if (currentDot) currentDot.remove();
        if (currentIcon) currentIcon.remove();

        if (dot) {
          var dotEl = document.createElement('span');
          dotEl.className = 'trigger-dot dot-' + dot;
          labelGroup.insertBefore(dotEl, labelSpan);
        } else if (icon) {
          var iconEl = document.createElement('i');
          iconEl.className = icon + ' trigger-icon';
          labelGroup.insertBefore(iconEl, labelSpan);
        }
      }

      // Update active option state in menu
      var menu = document.getElementById('menu_' + id);
      if (menu) {
        var items = menu.querySelectorAll('.rich-dropdown-item');
        items.forEach(function (item) {
          if (item.getAttribute('data-value') === value) {
            item.classList.add('is-selected');
            item.setAttribute('aria-selected', 'true');
          } else {
            item.classList.remove('is-selected');
            item.setAttribute('aria-selected', 'false');
          }
        });
      }

      // Close menu
      wrapper.classList.remove('is-open');
      if (trigger) trigger.setAttribute('aria-expanded', 'false');

      // Dispatch standard DOM 'change' event to trigger any existing listeners
      if (hiddenInput && oldValue !== value) {
        var changeEvt = new Event('change', { bubbles: true });
        hiddenInput.dispatchEvent(changeEvt);
      }

      // Call custom JS function callback if provided
      if (onChangeCallback && typeof global[onChangeCallback] === 'function') {
        global[onChangeCallback](value, label);
      }
    },

    closeAll: function () {
      var activeDropdowns = document.querySelectorAll('.rich-dropdown-wrapper.is-open');
      activeDropdowns.forEach(function (dd) {
        dd.classList.remove('is-open');
        var trigger = dd.querySelector('.rich-dropdown-trigger');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
      });
    }
  };

  // Close when clicking outside
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.rich-dropdown-wrapper')) {
      RichDropdown.closeAll();
    }
  });

  // Close when pressing Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
      RichDropdown.closeAll();
    }
  });

  global.RichDropdown = RichDropdown;
})(typeof window !== 'undefined' ? window : this);
