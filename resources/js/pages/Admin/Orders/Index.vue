<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Customer {
  id: number;
  name: string;
  email: string;
}

interface OrderItem {
  id: number;
  item_type: string;
  domain_name: string | null;
  quantity: number;
  price: number;
}

interface Order {
  id: number;
  total_amount: number;
  status: 'pending' | 'processing' | 'completed' | 'cancelled';
  billing_cycle: string;
  created_at: string;
  customer: Customer;
  order_items: OrderItem[];
}

interface Props {
  orders: {
    data: Order[];
    links: any[];
    meta: any;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Orders', href: '/admin/orders' },
];

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price);
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    case 'processing': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
    case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    case 'cancelled': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
  }
};

const totalRevenue = props.orders.data
  .filter(order => order.status === 'completed')
  .reduce((sum, order) => sum + order.total_amount, 0);
</script>

<template>
  <Head title="Admin - Orders" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Order Management</h1>
          <p class="text-muted-foreground">Track and manage customer orders</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-5">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Total Orders</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ orders.meta.total }}</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Completed</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">
              {{ orders.data.filter(o => o.status === 'completed').length }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Processing</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-blue-600">
              {{ orders.data.filter(o => o.status === 'processing').length }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Pending</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-yellow-600">
              {{ orders.data.filter(o => o.status === 'pending').length }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Revenue</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-xl font-bold">{{ formatPrice(totalRevenue) }}</div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>All Orders</CardTitle>
          <CardDescription>Complete list of customer orders</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-if="orders.data.length === 0" class="text-center py-8 text-muted-foreground">
              No orders found.
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="order in orders.data" 
                :key="order.id"
                class="flex items-center justify-between p-4 border rounded-lg hover:bg-muted/30"
              >
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="font-semibold">Order #{{ order.id }}</h3>
                    <span :class="`inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${getStatusColor(order.status)}`">
                      {{ order.status }}
                    </span>
                  </div>
                  <div class="text-sm text-muted-foreground space-y-1">
                    <div><strong>Customer:</strong> {{ order.customer.name }} ({{ order.customer.email }})</div>
                    <div><strong>Items:</strong> {{ order.order_items.length }} item(s)</div>
                    <div><strong>Billing:</strong> {{ order.billing_cycle.replace('_', ' ') }}</div>
                    <div><strong>Date:</strong> {{ formatDate(order.created_at) }}</div>
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <div class="text-right">
                    <div class="text-xl font-bold">{{ formatPrice(order.total_amount) }}</div>
                    <div class="text-sm text-muted-foreground">Total</div>
                  </div>
                  <Button variant="outline" size="sm" asChild>
                    <Link :href="`/admin/orders/${order.id}`">
                      View Details
                    </Link>
                  </Button>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="orders.links && orders.links.length > 3" class="flex justify-center gap-1 mt-6">
              <Link 
                v-for="link in orders.links" 
                :key="link.label"
                :href="link.url"
                :class="`px-3 py-2 text-sm border rounded ${
                  link.active 
                    ? 'bg-primary text-primary-foreground border-primary' 
                    : 'hover:bg-muted'
                } ${
                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                }`"
                v-html="link.label"
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>