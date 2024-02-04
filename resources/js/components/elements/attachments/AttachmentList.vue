<template>
  <EmptyState v-if="attachments.length === 0" subtitle="No attachment have been added yet"/>
  <div v-else class="mt-3">
    <p class="form-label mt-3">{{ title }}</p>
    <TableMain>
      <tbody>
      <tr v-for="(attachment, index) in attachments" :key="attachment">
        <td style="width: 40px">{{ index + 1 }}</td>
        <td class="text-start">
          <PreviewAttachment :href="attachment_url + attachment.path" :name="attachment.name"/>
        </td>
        <td v-if="canDelete" class="text-center p-1" style="min-width: 30px;">
          <button v-if="attachments.length > 1" @click="onConfirm(attachment.id)"
                  class="btn btn-danger ms-1">
            Delete
          </button>
        </td>
      </tr>
      </tbody>
    </TableMain>
  </div>
  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" :has-confirm="modal.hasConfirm"
         @confirm="onDelete"/>
</template>

<script>
import TableMain from "../TableMain.vue"
import EmptyState from "../EmptyState.vue";
import ConfirmationModal from "../ConfirmationModal.vue";
import Modal from "../Modal.vue";
import PreviewAttachment from "./PreviewAttachment.vue";

export default {
  components: {Modal, ConfirmationModal, EmptyState, TableMain, PreviewAttachment},
  emits: ['delete'],
  props: {
    title: {
      type: String,
      default: ''
    },
    attachment_url: {},
    attachments: {},
    canDelete: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      selectedId: null,
      modal: {
        show: false,
        loader: false,
        isClose: true,
        hasConfirm: true,
        message: 'The attachment will be removed permanently. Are you sure to continue?',

      },
    }
  },
  methods: {
    onConfirm(id) {
      this.selectedId = id;
      this.modal.show = true
    },
    onDelete() {
      this.modal.show = false;
      this.$emit('delete', this.selectedId);
    },

  }
}
</script>
