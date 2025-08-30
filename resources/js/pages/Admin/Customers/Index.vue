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
  phone?: string;
  city?: string;
  status: 'active' | 'inactive' | 'suspended';
  created_at: string;
  orders_count: number;
  services_count: number;
}

interface Props {
  customers: {
    data: Customer[];
    links: any[];
    meta: any;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Customers', href: '/admin/customers' },
];

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'active': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    case 'inactive': return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    case 'suspended': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
  }
};
</script>

<template>
  <Head title="Admin - Customers" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Customer Management</h1>
          <p class="text-muted-foreground">Manage customer accounts and information</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Total Customers</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ customers.meta.total }}</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Active</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">
              {{ customers.data.filter(c => c.status === 'active').length }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Suspended</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-red-600">
              {{ customers.data.filter(c => c.status === 'suspended').length }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Inactive</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-gray-600">
              {{ customers.data.filter(c => c.status === 'inactive').length }}
            </div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>All Customers</CardTitle>
          <CardDescription>List of all registered customers</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-if="customers.data.length === 0" class="text-center py-8 text-muted-foreground">
              No customers found.
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="customer in customers.data" 
                :key="customer.id"
                class="flex items-center justify-between p-4 border rounded-lg hover:bg-muted/30"
              >
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="font-semibold">{{ customer.name }}</h3>
                    <span :class="`inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${getStatusColor(customer.status)}`">
                      {{ customer.status }}
                    </span>
                  </div>
                  <div class="text-sm text-muted-foreground space-y-1">
                    <div>{{ customer.email }}</div>
                    <div v-if="customer.phone">{{ customer.phone }}</div>
                    <div v-if="customer.city">{{ customer.city }}</div>
                    <div>Joined {{ formatDate(customer.created_at) }}</div>
                  </div>
                </div>

                <div class="flex items-center gap-4 text-sm text-muted-foreground">
                  <div class="text-center">
                    <div class="font-medium text-foreground">{{ customer.orders_count }}</div>
                    <div>Orders</div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-foreground">{{ customer.services_count }}</div>
                    <div>Services</div>
                  </div>
                  <Button variant="outline" size="sm" asChild>
                    <Link :href="`/admin/customers/${customer.id}`">
                      View Details
                    </Link>
                  </Button>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="customers.links && customers.links.length > 3" class="flex justify-center gap-1 mt-6">
              <Link 
                v-for="link in customers.links" 
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