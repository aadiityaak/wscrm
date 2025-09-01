<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Search, Users, UserCheck, UserX, Clock, Plus, Edit, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

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
  customers?: {
    data: Customer[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: any[];
  };
  filters?: {
    search?: string;
    status?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedCustomer = ref<Customer | null>(null);

const createForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  city: '',
  country: '',
  postal_code: '',
});

const editForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  city: '',
  country: '',
  postal_code: '',
  status: 'active' as 'active' | 'inactive' | 'suspended',
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Customers', href: '/admin/customers' },
];

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const handleSearch = () => {
  router.get('/admin/customers', { 
    search: search.value, 
    status: status.value 
  }, { 
    preserveState: true,
    replace: true 
  });
};

const getStatusClass = (status: string) => {
  switch (status) {
    case 'active': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    case 'suspended': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    case 'inactive': return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
  }
};

const submitCreate = () => {
  createForm.post('/admin/customers', {
    onSuccess: () => {
      showCreateModal.value = false;
      createForm.reset();
    },
  });
};

const openEditModal = (customer: Customer) => {
  selectedCustomer.value = customer;
  editForm.reset();
  editForm.name = customer.name;
  editForm.email = customer.email;
  editForm.phone = customer.phone || '';
  editForm.address = customer.address || '';
  editForm.city = customer.city || '';
  editForm.country = customer.country || '';
  editForm.postal_code = customer.postal_code || '';
  editForm.status = customer.status;
  showEditModal.value = true;
};

const submitEdit = () => {
  if (!selectedCustomer.value) return;
  
  editForm.put(`/admin/customers/${selectedCustomer.value.id}`, {
    onSuccess: () => {
      showEditModal.value = false;
      editForm.reset();
      selectedCustomer.value = null;
    },
  });
};

const deleteCustomer = (customer: Customer) => {
  if (confirm(`Are you sure you want to delete ${customer.name}?`)) {
    router.delete(`/admin/customers/${customer.id}`);
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
        <Button @click="showCreateModal = true">
          <Plus class="h-4 w-4 mr-2" />
          Add Customer
        </Button>
      </div>

      <!-- Statistics Cards -->
      <div class="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Total Customers</CardTitle>
            <Users class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ customers?.total || 0 }}</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Active</CardTitle>
            <UserCheck class="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">
              {{ customers?.data?.filter(c => c.status === 'active').length || 0 }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Suspended</CardTitle>
            <UserX class="h-4 w-4 text-red-600" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-red-600">
              {{ customers?.data?.filter(c => c.status === 'suspended').length || 0 }}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">Inactive</CardTitle>
            <Clock class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-muted-foreground">
              {{ customers?.data?.filter(c => c.status === 'inactive').length || 0 }}
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Customer List -->
      <Card>
        <CardHeader>
          <CardTitle>Customers</CardTitle>
          <CardDescription>Manage customer accounts and information</CardDescription>
        </CardHeader>
        <CardContent>
          <!-- Search and Filter -->
          <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1 max-w-sm">
              <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                v-model="search"
                placeholder="Search customers..."
                class="pl-8"
                @keyup.enter="handleSearch"
              />
            </div>
            <select 
              v-model="status" 
              class="flex h-9 w-[180px] rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="suspended">Suspended</option>
            </select>
            <Button @click="handleSearch">Search</Button>
          </div>

          <!-- Customer Cards -->
          <div v-if="!customers?.data || customers.data.length === 0" class="text-center py-12 text-muted-foreground">
            <Users class="mx-auto h-12 w-12 text-muted-foreground/40" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No customers found</h3>
            <p class="mt-1 text-sm text-muted-foreground">Try adjusting your search criteria.</p>
          </div>

          <div v-else class="space-y-4">
            <div 
              v-for="customer in customers.data" 
              :key="customer.id"
              class="flex items-center justify-between p-4 border rounded-lg hover:bg-muted/30 transition-colors"
            >
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-sm font-semibold text-foreground truncate">{{ customer.name }}</h3>
                  <span :class="`inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${getStatusClass(customer.status)}`">
                    {{ customer.status }}
                  </span>
                </div>
                <div class="space-y-1 text-xs text-muted-foreground">
                  <div class="flex items-center gap-4">
                    <span>{{ customer.email }}</span>
                    <span v-if="customer.phone">{{ customer.phone }}</span>
                  </div>
                  <div class="flex items-center gap-4">
                    <span>ID: #{{ customer.id }}</span>
                    <span>Joined: {{ formatDate(customer.created_at) }}</span>
                    <span v-if="customer.city">{{ customer.city }}</span>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-6 ml-4">
                <!-- Statistics -->
                <div class="hidden md:flex items-center gap-4 text-xs text-muted-foreground">
                  <div class="text-center">
                    <div class="text-sm font-medium text-foreground">{{ customer.orders_count }}</div>
                    <div>Orders</div>
                  </div>
                  <div class="text-center">
                    <div class="text-sm font-medium text-foreground">{{ customer.services_count }}</div>
                    <div>Services</div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                  <Button size="sm" variant="outline" asChild>
                    <Link :href="`/admin/customers/${customer.id}`">
                      View Details
                    </Link>
                  </Button>
                  <Button size="sm" variant="outline" @click="openEditModal(customer)">
                    <Edit class="h-3 w-3" />
                  </Button>
                  <Button size="sm" variant="outline" @click="deleteCustomer(customer)">
                    <Trash2 class="h-3 w-3" />
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="customers?.links && customers.links.length > 3" class="flex items-center justify-between pt-6 border-t">
            <div class="text-sm text-muted-foreground">
              Showing {{ ((customers.current_page - 1) * customers.per_page + 1) || 0 }} to 
              {{ Math.min(customers.current_page * customers.per_page, customers.total) || 0 }} of 
              {{ customers.total || 0 }} results
            </div>
            <div class="flex items-center gap-1">
              <template v-for="link in customers.links" :key="link.label">
                <Button 
                  v-if="link.url" 
                  variant="outline" 
                  size="sm"
                  :disabled="!link.url"
                  :class="link.active ? 'bg-primary text-primary-foreground' : ''"
                  @click="router.visit(link.url)"
                  v-html="link.label"
                />
                <span 
                  v-else 
                  class="px-3 py-2 text-sm text-muted-foreground"
                  v-html="link.label"
                />
              </template>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Create Customer Modal -->
    <Dialog v-model:open="showCreateModal">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Add New Customer</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="submitCreate" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="create-name">Name *</Label>
              <Input
                id="create-name"
                v-model="createForm.name"
                :class="{ 'border-red-500': createForm.errors.name }"
                required
              />
              <p v-if="createForm.errors.name" class="text-xs text-red-500 mt-1">{{ createForm.errors.name }}</p>
            </div>
            <div>
              <Label for="create-email">Email *</Label>
              <Input
                id="create-email"
                type="email"
                v-model="createForm.email"
                :class="{ 'border-red-500': createForm.errors.email }"
                required
              />
              <p v-if="createForm.errors.email" class="text-xs text-red-500 mt-1">{{ createForm.errors.email }}</p>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="create-password">Password *</Label>
              <Input
                id="create-password"
                type="password"
                v-model="createForm.password"
                :class="{ 'border-red-500': createForm.errors.password }"
                required
              />
              <p v-if="createForm.errors.password" class="text-xs text-red-500 mt-1">{{ createForm.errors.password }}</p>
            </div>
            <div>
              <Label for="create-password-confirmation">Confirm Password *</Label>
              <Input
                id="create-password-confirmation"
                type="password"
                v-model="createForm.password_confirmation"
                required
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="create-phone">Phone</Label>
              <Input
                id="create-phone"
                v-model="createForm.phone"
                :class="{ 'border-red-500': createForm.errors.phone }"
              />
              <p v-if="createForm.errors.phone" class="text-xs text-red-500 mt-1">{{ createForm.errors.phone }}</p>
            </div>
            <div>
              <Label for="create-city">City</Label>
              <Input
                id="create-city"
                v-model="createForm.city"
                :class="{ 'border-red-500': createForm.errors.city }"
              />
              <p v-if="createForm.errors.city" class="text-xs text-red-500 mt-1">{{ createForm.errors.city }}</p>
            </div>
          </div>

          <div>
            <Label for="create-address">Address</Label>
            <Input
              id="create-address"
              v-model="createForm.address"
              :class="{ 'border-red-500': createForm.errors.address }"
            />
            <p v-if="createForm.errors.address" class="text-xs text-red-500 mt-1">{{ createForm.errors.address }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="create-country">Country</Label>
              <Input
                id="create-country"
                v-model="createForm.country"
                :class="{ 'border-red-500': createForm.errors.country }"
              />
              <p v-if="createForm.errors.country" class="text-xs text-red-500 mt-1">{{ createForm.errors.country }}</p>
            </div>
            <div>
              <Label for="create-postal-code">Postal Code</Label>
              <Input
                id="create-postal-code"
                v-model="createForm.postal_code"
                :class="{ 'border-red-500': createForm.errors.postal_code }"
              />
              <p v-if="createForm.errors.postal_code" class="text-xs text-red-500 mt-1">{{ createForm.errors.postal_code }}</p>
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="showCreateModal = false">
              Cancel
            </Button>
            <Button type="submit" :disabled="createForm.processing">
              {{ createForm.processing ? 'Creating...' : 'Create Customer' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Edit Customer Modal -->
    <Dialog v-model:open="showEditModal">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Edit Customer</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="submitEdit" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="edit-name">Name *</Label>
              <Input
                id="edit-name"
                v-model="editForm.name"
                :class="{ 'border-red-500': editForm.errors.name }"
                required
              />
              <p v-if="editForm.errors.name" class="text-xs text-red-500 mt-1">{{ editForm.errors.name }}</p>
            </div>
            <div>
              <Label for="edit-email">Email *</Label>
              <Input
                id="edit-email"
                type="email"
                v-model="editForm.email"
                :class="{ 'border-red-500': editForm.errors.email }"
                required
              />
              <p v-if="editForm.errors.email" class="text-xs text-red-500 mt-1">{{ editForm.errors.email }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="edit-password">New Password (optional)</Label>
              <Input
                id="edit-password"
                type="password"
                v-model="editForm.password"
                :class="{ 'border-red-500': editForm.errors.password }"
              />
              <p v-if="editForm.errors.password" class="text-xs text-red-500 mt-1">{{ editForm.errors.password }}</p>
            </div>
            <div>
              <Label for="edit-password-confirmation">Confirm Password</Label>
              <Input
                id="edit-password-confirmation"
                type="password"
                v-model="editForm.password_confirmation"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label for="edit-phone">Phone</Label>
              <Input
                id="edit-phone"
                v-model="editForm.phone"
                :class="{ 'border-red-500': editForm.errors.phone }"
              />
              <p v-if="editForm.errors.phone" class="text-xs text-red-500 mt-1">{{ editForm.errors.phone }}</p>
            </div>
            <div>
              <Label for="edit-city">City</Label>
              <Input
                id="edit-city"
                v-model="editForm.city"
                :class="{ 'border-red-500': editForm.errors.city }"
              />
              <p v-if="editForm.errors.city" class="text-xs text-red-500 mt-1">{{ editForm.errors.city }}</p>
            </div>
          </div>

          <div>
            <Label for="edit-address">Address</Label>
            <Input
              id="edit-address"
              v-model="editForm.address"
              :class="{ 'border-red-500': editForm.errors.address }"
            />
            <p v-if="editForm.errors.address" class="text-xs text-red-500 mt-1">{{ editForm.errors.address }}</p>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <Label for="edit-country">Country</Label>
              <Input
                id="edit-country"
                v-model="editForm.country"
                :class="{ 'border-red-500': editForm.errors.country }"
              />
              <p v-if="editForm.errors.country" class="text-xs text-red-500 mt-1">{{ editForm.errors.country }}</p>
            </div>
            <div>
              <Label for="edit-postal-code">Postal Code</Label>
              <Input
                id="edit-postal-code"
                v-model="editForm.postal_code"
                :class="{ 'border-red-500': editForm.errors.postal_code }"
              />
              <p v-if="editForm.errors.postal_code" class="text-xs text-red-500 mt-1">{{ editForm.errors.postal_code }}</p>
            </div>
            <div>
              <Label for="edit-status">Status *</Label>
              <select 
                id="edit-status"
                v-model="editForm.status"
                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                required
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
              </select>
              <p v-if="editForm.errors.status" class="text-xs text-red-500 mt-1">{{ editForm.errors.status }}</p>
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="showEditModal = false">
              Cancel
            </Button>
            <Button type="submit" :disabled="editForm.processing">
              {{ editForm.processing ? 'Updating...' : 'Update Customer' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>