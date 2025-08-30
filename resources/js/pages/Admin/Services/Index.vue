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

interface HostingPlan {
  id: number;
  plan_name: string;
  storage_gb: number;
  cpu_cores: number;
  ram_gb: number;
}

interface Service {
  id: number;
  service_type: 'hosting' | 'domain';
  domain_name: string;
  status: 'active' | 'suspended' | 'terminated' | 'pending';
  expires_at: string;
  auto_renew: boolean;
  created_at: string;
  customer: Customer;
  hosting_plan?: HostingPlan;
}

interface Props {
  services: {
    data: Service[];
    links: any[];
    meta: any;
  };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Services', href: '/admin/services' },
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
    case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    case 'suspended': return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
    case 'terminated': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
  }
};

const isExpiringSoon = (expiresAt: string) => {
  const expiryDate = new Date(expiresAt);
  const thirtyDaysFromNow = new Date();
  thirtyDaysFromNow.setDate(thirtyDaysFromNow.getDate() + 30);
  return expiryDate <= thirtyDaysFromNow;
};

const expiringSoon = props.services.data.filter(s => 
  s.status === 'active' && isExpiringSoon(s.expires_at)
).length;
</script>

<template>
  <Head title="Admin - Services" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Service Management</h1>
          <p class="text-muted-foreground">Manage customer hosting and domain services</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-5">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Total Services</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ services.meta.total }}</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Active</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">
              {{ services.data.filter(s => s.status === 'active').length }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Suspended</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-orange-600">
              {{ services.data.filter(s => s.status === 'suspended').length }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Expiring Soon</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-red-600">{{ expiringSoon }}</div>
            <div class="text-xs text-muted-foreground">Next 30 days</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Hosting</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-blue-600">
              {{ services.data.filter(s => s.service_type === 'hosting').length }}
            </div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>All Services</CardTitle>
          <CardDescription>Complete list of customer services</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-if="services.data.length === 0" class="text-center py-8 text-muted-foreground">
              No services found.
            </div>
            
            <div v-else class="space-y-4">
              <div 
                v-for="service in services.data" 
                :key="service.id"
                class="flex items-center justify-between p-4 border rounded-lg hover:bg-muted/30"
              >
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="font-semibold">{{ service.domain_name }}</h3>
                    <span :class="`inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${
                      service.service_type === 'hosting' 
                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                        : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
                    }`">
                      {{ service.service_type }}
                    </span>
                    <span :class="`inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${getStatusColor(service.status)}`">
                      {{ service.status }}
                    </span>
                    <span v-if="isExpiringSoon(service.expires_at) && service.status === 'active'" 
                          class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                      Expiring Soon
                    </span>
                  </div>
                  <div class="text-sm text-muted-foreground space-y-1">
                    <div><strong>Customer:</strong> {{ service.customer.name }} ({{ service.customer.email }})</div>
                    <div v-if="service.hosting_plan">
                      <strong>Plan:</strong> {{ service.hosting_plan.plan_name }} 
                      ({{ service.hosting_plan.storage_gb }}GB, {{ service.hosting_plan.cpu_cores }} CPU, {{ service.hosting_plan.ram_gb }}GB RAM)
                    </div>
                    <div><strong>Created:</strong> {{ formatDate(service.created_at) }}</div>
                    <div><strong>Expires:</strong> {{ formatDate(service.expires_at) }}</div>
                    <div><strong>Auto Renew:</strong> {{ service.auto_renew ? 'Yes' : 'No' }}</div>
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <Button variant="outline" size="sm" asChild>
                    <Link :href="`/admin/services/${service.id}`">
                      Manage
                    </Link>
                  </Button>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="services.links && services.links.length > 3" class="flex justify-center gap-1 mt-6">
              <Link 
                v-for="link in services.links" 
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