<template>
  <DefaultField :field="currentField" :full-width-content="currentField.fullWidth" :show-help-text="showHelpText">
    <template #field>
      <div class="nova-items-field-input-wrapper flex mb-2" v-if="currentField.listFirst === false && ! maxReached">
        <input
            v-model="newItem"
            :type="currentField.inputType"
            :placeholder="currentField.placeholder"
            autocomplete="new-password"
            @keydown.enter.prevent="addItem"
            class="flex-1 form-control form-input form-input-bordered"
        />
        <button
            type="button"
            @click="addItem"
            v-html="currentField.createButtonValue"
            v-if="currentField.hideCreateButton === false"
            class="ml-3 cursor-pointer shadow relative bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-900"
        />
      </div>
      <ul ref="novaitemslist" :style="maxHeight" v-if="items.length" class="nova-items-field-input-items list-reset">
        <draggable :disabled="currentField.draggable === false" v-model="items"
                   :item-key="currentField.attribute + '.' + index" handle=".sortable-handle">
          <template #item="{ element, index }">
            <li class="py-1">
              <div class="nova-items-field-input-wrapper-item flex py-1 gap-2">
                <button type="button" v-if="currentField.draggable === true" class="cursor-move sortable-handle px-4">
                  <Icon name="menu" />
                </button>
                <input
                    :value="element"
                    :type="currentField.inputType"
                    v-on:keyup="updateItem(index, $event)"
                    :name="currentField.name + '['+ index +']'"
                    autocomplete="new-password"
                    :class="{'border-danger': hasErrors(index)}"
                    class="flex-1 form-control form-input form-input-bordered"
                >
                <button
                    v-if="currentField.deleteButtonValue"
                    type="button"
                    @click="removeItem(index)"
                    class="px-4 text-xl font-bold focus:outline-none focus:ring"
                    v-html="currentField.deleteButtonValue"
                />
                <button v-else type="button" @click="removeItem(index)" class="px-1 ml-1 toolbar-button">
                  <Icon name="x" />
                </button>
              </div>
              <HelpText class="mt-2 help-text-error" v-if="hasErrors(index)">
                {{ arrayErrors[index][0] }}
              </HelpText>
            </li>
          </template>
        </draggable>
      </ul>
      <div class="nova-items-field-input-wrapper flex mt-2" v-if="currentField.listFirst && ! maxReached">
        <input
            v-model="newItem"
            :type="currentField.inputType"
            :placeholder="currentField.placeholder"
            class="flex-1 form-control form-input form-input-bordered"
            @keydown.enter.prevent="addItem"
        />
        <button
            type="button"
            @click="addItem"
            v-html="currentField.createButtonValue"
            v-if="currentField.hideCreateButton === false"
            class="ml-3 cursor-pointer shadow relative bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-900"
        />
      </div>
      <HelpText class="mt-2 help-text-error" v-if="hasErrors()">
        {{ arrayErrors[''][0] }}
      </HelpText>
    </template>
  </DefaultField>
</template>

<style scoped>
</style>

<script>
import draggable from 'vuedraggable'
import {DependentFormField, HandlesValidationErrors} from 'laravel-nova'
import HasFieldValue from "../mixins/HasFieldValue";
import { Icon } from 'laravel-nova-ui'

export default {
  mixins: [DependentFormField, HandlesValidationErrors, HasFieldValue],

  props: ['resourceName', 'resourceId', 'field'],

  components: {draggable,Icon},

  data()
  {
    return {
      value: '',
      items: [],
      newItem: '',
      arrayErrors: []
    };
  },

  methods: {
    setInitialValue()
    {
      this.value = JSON.stringify(this.fieldValue);
      this.items = this.fieldValue ?? [];
    },

    fill(formData)
    {
      formData.append(this.field.attribute, this.value || [])
    },

    addItem()
    {
      let item, go_ahead;
      if (typeof(this.newItem) === 'number') {
        item = this.newItem;
        go_ahead = true;
      } else {
        item = this.newItem.trim();
        go_ahead = item !== '';
      }
      if (go_ahead && !this.maxReached) {
        this.items.push(item)
        this.newItem = ''

        this.$nextTick(() => {
          if (this.field.maxHeight) {
            this.$refs.novaitemslist.scrollTop = this.$refs.novaitemslist.scrollHeight;
          }
        })
      }
    },

    updateItem(index, event)
    {
      this.items[index] = event.target.value
    },

    removeItem(index)
    {
      this.items.splice(index, 1)
    },

    hasErrors(key)
    {
      return this.arrayErrors.hasOwnProperty(key ?? '');
    }
  },
  computed: {
    maxHeight()
    {
      if (this.field.maxHeight === false) {
        return '';
      }

      return `max-height: ${this.field.maxHeight}px; overflow: auto;`;
    },
    maxReached()
    {
      return this.field.max !== false && this.items.length + 1 > this.field.max;
    }
  },
  watch: {
    'items': {
      handler: function (items) {
        this.value = JSON.stringify(items);
      },
      deep: true
    },
    'errors': {
      handler: function (errors) {
        this.arrayErrors = errors.errors.hasOwnProperty(this.field.validationKey) ? JSON.parse(errors.errors[this.field.validationKey][0]) : {};
      },
      deep: true
    }
  }
}
</script>
