<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Plus, Edit, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface DomainPrice {
  id: number;
  extension: string;
  register_price: number;
  renew_price: number;
  transfer_price: number;
  is_active: boolean;
}

interface Props {
  domainPrices: {
    data: DomainPrice[];
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
  { title: 'Domain Prices', href: '/admin/domain-prices' },
];

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price);
};

const handleSearch = () => {
  router.get('/admin/domain-prices', { search: search.value }, { 
    preserveState: true,
    replace: true 
  });
};

const deleteDomainPrice = (id: number) => {
  if (confirm('Are you sure you want to delete this domain price?')) {
    router.delete(`/admin/domain-prices/${id}`);
  }
};
</script>

<template>
  <Head title="Domain Prices Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Domain Prices</h1>
          <p class="text-muted-foreground">Manage domain extension pricing</p>
        </div>
        <Button asChild>
          <Link href="/admin/domain-prices/create">
            <Plus class="h-4 w-4 mr-2" />
            Add Domain Price
          </Link>
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Domain Prices</CardTitle>
          <CardDescription>
            Manage pricing for different domain extensions
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="mb-4 flex items-center space-x-2">
            <div class="relative flex-1 max-w-sm">
              <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                v-model="search"
                placeholder="Search domain extensions..."
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
                  <TableHead>Extension</TableHead>
                  <TableHead>Register Price</TableHead>
                  <TableHead>Renew Price</TableHead>
                  <TableHead>Transfer Price</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead class="w-[100px]">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="domain in domainPrices.data" :key="domain.id">
                  <TableCell class="font-medium">.{{ domain.extension }}</TableCell>
                  <TableCell>{{ formatPrice(domain.register_price) }}</TableCell>
                  <TableCell>{{ formatPrice(domain.renew_price) }}</TableCell>
                  <TableCell>{{ formatPrice(domain.transfer_price) }}</TableCell>
                  <TableCell>
                    <Badge :variant="domain.is_active ? 'default' : 'secondary'">
                      {{ domain.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                  </TableCell>
                  <TableCell>
                    <div class="flex items-center space-x-2">
                      <Button size="sm" variant="outline" asChild>
                        <Link :href="`/admin/domain-prices/${domain.id}/edit`">
                          <Edit class="h-3 w-3" />
                        </Link>
                      </Button>
                      <Button 
                        size="sm" 
                        variant="outline"
                        @click="deleteDomainPrice(domain.id)"
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
              Showing {{ (domainPrices.current_page - 1) * domainPrices.per_page + 1 }} to 
              {{ Math.min(domainPrices.current_page * domainPrices.per_page, domainPrices.total) }} of 
              {{ domainPrices.total }} results
            </div>
            <div class="flex items-center space-x-2">
              <template v-for="link in domainPrices.links" :key="link.label">
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