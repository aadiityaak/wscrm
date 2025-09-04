<script setup lang="ts">
interface MonthlyStats {
  month: string
  month_short: string
  orders: number
  revenue: number
  customers: number
}

interface Props {
  data: MonthlyStats[]
}

const props = defineProps<Props>()

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price);
}

const formatCompactPrice = (price: number) => {
  if (price >= 1000000) {
    return (price / 1000000).toFixed(1) + 'M'
  }
  if (price >= 1000) {
    return (price / 1000).toFixed(0) + 'K'
  }
  return price.toString()
}
</script>

<template>
  <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
    <div
      v-for="(month, index) in data"
      :key="month.month"
      class="relative overflow-hidden rounded-lg border bg-card p-4 hover:shadow-md transition-shadow"
      :class="{
        'border-primary bg-primary/5': index === data.length - 1,
      }"
    >
      <!-- Month header -->
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-sm" :class="{ 'text-primary': index === data.length - 1 }">
          {{ month.month }}
        </h3>
        <div 
          v-if="index === data.length - 1" 
          class="px-2 py-1 bg-primary/10 text-primary text-xs rounded-full font-medium"
        >
          Current
        </div>
      </div>
      
      <!-- Stats grid -->
      <div class="space-y-3">
        <!-- Orders -->
        <div class="flex items-center justify-between">
          <span class="text-xs text-muted-foreground flex items-center gap-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            Orders
          </span>
          <span class="font-bold text-sm">{{ month.orders }}</span>
        </div>
        
        <!-- Revenue -->
        <div class="flex items-center justify-between">
          <span class="text-xs text-muted-foreground flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            Revenue
          </span>
          <span class="font-bold text-sm" :title="formatPrice(month.revenue)">
            {{ formatCompactPrice(month.revenue) }}
          </span>
        </div>
        
        <!-- Customers -->
        <div class="flex items-center justify-between">
          <span class="text-xs text-muted-foreground flex items-center gap-2">
            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
            New Customers
          </span>
          <span class="font-bold text-sm">{{ month.customers }}</span>
        </div>
      </div>
      
      <!-- Background decoration -->
      <div 
        class="absolute -bottom-4 -right-4 w-16 h-16 opacity-5 transform rotate-12"
        :class="{ 'text-primary': index === data.length - 1, 'text-muted-foreground': index !== data.length - 1 }"
      >
        <svg fill="currentColor" viewBox="0 0 24 24">
          <path d="M7 4V2a1 1 0 00-2 0v2H3a1 1 0 000 2h2v2a1 1 0 002 0V6h2a1 1 0 000-2H7zM17 12V10a1 1 0 00-2 0v2h-2a1 1 0 000 2h2v2a1 1 0 002 0v-2h2a1 1 0 000-2h-2zM7 14v-2a1 1 0 00-2 0v2H3a1 1 0 000 2h2v2a1 1 0 002 0v-2h2a1 1 0 000-2H7z"/>
        </svg>
      </div>
    </div>
  </div>
</template>