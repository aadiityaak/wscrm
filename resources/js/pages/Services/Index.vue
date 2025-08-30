<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

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
  hosting_plan?: HostingPlan;
  created_at: string;
}

interface Props {
  services: Service[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'My Services', href: '/services' },
];

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
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

const getStatusText = (status: string) => {
  switch (status) {
    case 'active': return 'Active';
    case 'pending': return 'Pending';
    case 'suspended': return 'Suspended';
    case 'terminated': return 'Terminated';
    default: return status;
  }
};

const isExpiringSoon = (expiresAt: string) => {
  const expiryDate = new Date(expiresAt);
  const thirtyDaysFromNow = new Date();
  thirtyDaysFromNow.setDate(thirtyDaysFromNow.getDate() + 30);
  return expiryDate <= thirtyDaysFromNow;
};

const daysUntilExpiry = (expiresAt: string) => {
  const expiryDate = new Date(expiresAt);
  const today = new Date();
  const diffTime = expiryDate.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays;
};
</script>

<template>
  <Head title="My Services" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">My Services</h1>
          <p class="text-muted-foreground">Manage your hosting and domain services</p>
        </div>
        <Button asChild>
          <Link href="/hosting">Add New Service</Link>
        </Button>
      </div>

      <div v-if="services.length === 0" class="text-center py-12">
        <div class="text-muted-foreground">
          <p class="text-lg mb-4">You don't have any active services yet.</p>
          <Button asChild>
            <Link href="/hosting">Get Started</Link>
          </Button>
        </div>
      </div>

      <div v-else class="space-y-4">
        <Card v-for="service in services" :key="service.id">
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle class="flex items-center gap-2">
                  {{ service.domain_name }}
                  <span :class="`inline-flex items-center rounded px-2 py-1 text-xs font-medium ${
                    service.service_type === 'hosting' 
                      ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                      : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
                  }`">
                    {{ service.service_type }}
                  </span>
                </CardTitle>
                <CardDescription>
                  Created {{ formatDate(service.created_at) }}
                  <span v-if="service.hosting_plan" class="ml-2">
                    • {{ service.hosting_plan.plan_name }} ({{ service.hosting_plan.storage_gb }}GB)
                  </span>
                </CardDescription>
              </div>
              <div class="text-right space-y-1">
                <span :class="`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${getStatusColor(service.status)}`">
                  {{ getStatusText(service.status) }}
                </span>
                <div class="text-sm text-muted-foreground">
                  Auto-renew: {{ service.auto_renew ? 'On' : 'Off' }}
                </div>
              </div>
            </div>
          </CardHeader>
          
          <CardContent>
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-muted-foreground">Expires</span>
                <div class="text-right">
                  <div class="font-medium">{{ formatDate(service.expires_at) }}</div>
                  <div 
                    v-if="service.status === 'active'"
                    :class="`text-xs ${
                      isExpiringSoon(service.expires_at) 
                        ? 'text-orange-600 dark:text-orange-400' 
                        : 'text-muted-foreground'
                    }`"
                  >
                    {{ daysUntilExpiry(service.expires_at) }} days remaining
                  </div>
                </div>
              </div>

              <div v-if="service.hosting_plan" class="grid grid-cols-3 gap-4 text-sm">
                <div>
                  <span class="text-muted-foreground">Storage</span>
                  <div class="font-medium">{{ service.hosting_plan.storage_gb }}GB</div>
                </div>
                <div>
                  <span class="text-muted-foreground">CPU</span>
                  <div class="font-medium">{{ service.hosting_plan.cpu_cores }} cores</div>
                </div>
                <div>
                  <span class="text-muted-foreground">RAM</span>
                  <div class="font-medium">{{ service.hosting_plan.ram_gb }}GB</div>
                </div>
              </div>
              
              <div class="flex justify-end gap-2 pt-2">
                <Button variant="outline" size="sm" asChild>
                  <Link :href="`/services/${service.id}`">Manage</Link>
                </Button>
                <Button 
                  v-if="isExpiringSoon(service.expires_at) && service.status === 'active'" 
                  size="sm"
                >
                  Renew Now
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>