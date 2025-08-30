<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Plus, Edit, Trash2, Server, HardDrive, Cpu, MemoryStick } from 'lucide-vue-next';
import { ref } from 'vue';

interface HostingPlan {
  id: number;
  plan_name: string;
  storage_gb: number;
  bandwidth_gb: number;
  cpu_cores: number;
  ram_gb: number;
  price_monthly: number;
  price_yearly: number;
  features: string[];
  is_active: boolean;
}

interface Props {
  hostingPlans: {
    data: HostingPlan[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: any[];
  };
  filters: {
    search?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Hosting Plans', href: '/admin/hosting-plans' },
];

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price);
};

const handleSearch = () => {
  router.get('/admin/hosting-plans', { search: search.value }, { 
    preserveState: true,
    replace: true 
  });
};

const deleteHostingPlan = (id: number) => {
  if (confirm('Are you sure you want to delete this hosting plan?')) {
    router.delete(`/admin/hosting-plans/${id}`);
  }
};
</script>

<template>
  <Head title="Hosting Plans Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Hosting Plans</h1>
          <p class="text-muted-foreground">Manage hosting plan configurations</p>
        </div>
        <Button asChild>
          <Link href="/admin/hosting-plans/create">
            <Plus class="h-4 w-4 mr-2" />
            Add Hosting Plan
          </Link>
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Hosting Plans</CardTitle>
          <CardDescription>
            Manage hosting plans and their specifications
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="mb-4 flex items-center space-x-2">
            <div class="relative flex-1 max-w-sm">
              <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                v-model="search"
                placeholder="Search hosting plans..."
                class="pl-8"
                @keyup.enter="handleSearch"
              />
            </div>
            <Button @click="handleSearch">Search</Button>
          </div>

          <div class="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Plan Name</TableHead>
                  <TableHead>Specifications</TableHead>
                  <TableHead>Monthly Price</TableHead>
                  <TableHead>Yearly Price</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead class="w-[100px]">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="plan in hostingPlans.data" :key="plan.id">
                  <TableCell class="font-medium">{{ plan.plan_name }}</TableCell>
                  <TableCell>
                    <div class="flex flex-wrap gap-2 text-xs">
                      <div class="flex items-center space-x-1 bg-muted px-2 py-1 rounded">
                        <HardDrive class="h-3 w-3" />
                        <span>{{ plan.storage_gb }}GB Storage</span>
                      </div>
                      <div class="flex items-center space-x-1 bg-muted px-2 py-1 rounded">
                        <Cpu class="h-3 w-3" />
                        <span>{{ plan.cpu_cores }} CPU</span>
                      </div>
                      <div class="flex items-center space-x-1 bg-muted px-2 py-1 rounded">
                        <MemoryStick class="h-3 w-3" />
                        <span>{{ plan.ram_gb }}GB RAM</span>
                      </div>
                    </div>
                  </TableCell>
                  <TableCell>{{ formatPrice(plan.price_monthly) }}</TableCell>
                  <TableCell>{{ formatPrice(plan.price_yearly) }}</TableCell>
                  <TableCell>
                    <Badge :variant="plan.is_active ? 'default' : 'secondary'">
                      {{ plan.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                  </TableCell>
                  <TableCell>
                    <div class="flex items-center space-x-2">
                      <Button size="sm" variant="outline" asChild>
                        <Link :href="`/admin/hosting-plans/${plan.id}/edit`">
                          <Edit class="h-3 w-3" />
                        </Link>
                      </Button>
                      <Button 
                        size="sm" 
                        variant="outline"
                        @click="deleteHostingPlan(plan.id)"
                      >
                        <Trash2 class="h-3 w-3" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Pagination -->
          <div class="flex items-center justify-between pt-4">
            <div class="text-sm text-muted-foreground">
              Showing {{ (hostingPlans.current_page - 1) * hostingPlans.per_page + 1 }} to 
              {{ Math.min(hostingPlans.current_page * hostingPlans.per_page, hostingPlans.total) }} of 
              {{ hostingPlans.total }} results
            </div>
            <div class="flex items-center space-x-2">
              <template v-for="link in hostingPlans.links" :key="link.label">
                <Button 
                  v-if="link.url" 
                  variant="outline" 
                  size="sm" 
                  :disabled="!link.url"
                  @click="router.visit(link.url)"
                  v-html="link.label"
                />
              </template>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>