import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'

export function useAdminFeedback() {
  const toast = useToast()
  const confirm = useConfirm()

  const success = (detail, summary = 'انجام شد') => {
    toast.add({ severity: 'success', summary, detail, life: 2800 })
  }

  const error = (detail, summary = 'خطا') => {
    toast.add({ severity: 'error', summary, detail, life: 4500 })
  }

  const info = (detail, summary = '') => {
    toast.add({ severity: 'info', summary: summary || 'توجه', detail, life: 3500 })
  }

  /**
   * @param {{ message: string, header?: string, acceptLabel?: string, rejectLabel?: string, accept?: () => void }} opts
   */
  const confirmDelete = (opts) => {
    confirm.require({
      message: opts.message,
      header: opts.header || 'تأیید عملیات',
      icon: 'pi pi-exclamation-triangle',
      acceptLabel: opts.acceptLabel || 'حذف',
      rejectLabel: opts.rejectLabel || 'انصراف',
      acceptClass: 'p-button-danger',
      rejectClass: 'p-button-text',
      accept: opts.accept
    })
  }

  return { success, error, info, confirmDelete, confirmDialog: confirm }
}
