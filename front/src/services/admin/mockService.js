const randomId = () => Math.floor(Math.random() * 10000)

export const adminMockService = {
  dashboardStats() {
    return [
      { title: 'دسته بندی ها', value: '12', icon: 'pi pi-list' },
      { title: 'آیتم های منو', value: '48', icon: 'pi pi-box' },
      { title: 'کاربران', value: '932', icon: 'pi pi-users' },
      { title: 'تخفیف های فعال', value: '76', icon: 'pi pi-percentage' }
    ]
  },
  tableRows() {
    return Array.from({ length: 8 }).map((_, i) => ({
      id: randomId(),
      name: `رکورد نمونه ${i + 1}`,
      updatedAt: '1405/02/16',
      statusLabel: i % 2 ? 'فعال' : 'غیرفعال',
      statusSeverity: i % 2 ? 'success' : 'danger'
    }))
  }
}
