export const mapDashboardSummary = (payload = {}) => {
  const stats = Array.isArray(payload?.stats) ? payload.stats : []

  return stats.map((stat) => ({
    title: stat?.title || '',
    value: String(stat?.value ?? ''),
    icon: stat?.icon || 'pi pi-chart-bar'
  }))
}
