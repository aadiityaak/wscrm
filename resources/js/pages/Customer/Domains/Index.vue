<script setup lang="ts">
import CustomerLayout from '@/layouts/CustomerLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Globe, ShoppingCart, TrendingUp, Crown, Star } from 'lucide-vue-next';
import { ref } from 'vue';

interface DomainPrice {
  id: number;
  extension: string;
  base_cost: number;
  renewal_cost: number;
  selling_price: number;
  renewal_price_with_tax: number;
  is_active: boolean;
}

interface Props {
  domainPrices: DomainPrice[];
  filters: {
    search?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const domainSearch = ref('');

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/customer/dashboard' },
  { title: 'Domains', href: '/customer/domains' },
];

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price);
};

const handleSearch = () => {
  router.get('/customer/domains', { search: search.value }, { 
    preserveState: true,
    replace: true 
  });
};

const searchDomain = () => {
  if (domainSearch.value.trim()) {
    router.get('/customer/domains/search', { domain: domainSearch.value });
  }
};

const getPopularExtensions = () => {
  return props.domainPrices
    .filter(domain => ['com', 'net', 'org', 'info', 'id'].includes(domain.extension))
    .sort((a, b) => a.selling_price - b.selling_price);
};

const getAffordableExtensions = () => {
  return props.domainPrices
    .sort((a, b) => a.selling_price - b.selling_price)
    .slice(0, 6);
};

const isPremium = (extension: string) => {
  return ['com', 'net', 'org'].includes(extension);
};

const orderDomain = (domainPriceId: number) => {
  router.post('/customer/orders', {
    order_type: 'domain',
    items: [{
      item_type: 'domain',
      item_id: domainPriceId,
      domain_name: domainSearch.value || 'example',
      quantity: 1
    }]
  });
};
</script>

<template>
  <Head title="Domain Registration" />

  <CustomerLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-8 p-6">
      <!-- Hero Section -->
      <div class="text-center space-y-6 py-12 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl">
        <div class="space-y-4">
          <h1 class="text-4xl font-bold tracking-tight">Find Your Perfect Domain</h1>
          <p class="text-xl text-muted-foreground max-w-3xl mx-auto">
            Search for available domains and register them instantly. Your online presence starts here.
          </p>
        </div>

        <!-- Domain Search -->
        <Card class="max-w-2xl mx-auto">
          <CardContent class="pt-6">
            <div class="flex items-center space-x-2">
              <div class="relative flex-1">
                <Globe class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                <Input
                  v-model="domainSearch"
                  placeholder="Enter your domain name (e.g., mywebsite.com)"
                  class="pl-11 h-12 text-lg"
                  @keyup.enter="searchDomain"
                />
              </div>
              <Button @click="searchDomain" size="lg" class="px-8">
                <Search class="h-5 w-5 mr-2" />
                Search
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Popular Extensions -->
      <div class="space-y-6">
        <div class="text-center space-y-2">
          <h2 class="text-3xl font-bold">Popular Domain Extensions</h2>
          <p class="text-muted-foreground">Most trusted and widely used domain extensions</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 max-w-6xl mx-auto">
          <Card v-for="domain in getPopularExtensions()" :key="domain.id" 
                class="relative overflow-hidden hover:shadow-lg transition-all cursor-pointer group"
                @click="orderDomain(domain.id)">
            <div v-if="isPremium(domain.extension)" class="absolute top-2 right-2">
              <Crown class="h-4 w-4 text-yellow-500" />
            </div>
            
            <CardContent class="pt-6 text-center space-y-4">
              <div class="text-3xl font-bold text-blue-600">.{{ domain.extension }}</div>
              
              <div class="space-y-1">
                <div class="text-2xl font-bold">{{ formatPrice(domain.selling_price) }}</div>
                <div class="text-sm text-muted-foreground">per year</div>
              </div>

              <Button class="w-full group-hover:bg-blue-600 transition-colors">
                <ShoppingCart class="h-4 w-4 mr-2" />
                Register
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- All Extensions -->
      <div class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold">All Domain Extensions</h2>
            <p class="text-muted-foreground">Browse all available domain extensions</p>
          </div>
          
          <!-- Extension Search -->
          <Card class="w-80">
            <CardContent class="pt-4">
              <div class="flex items-center space-x-2">
                <div class="relative flex-1">
                  <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                  <Input
                    v-model="search"
                    placeholder="Filter extensions..."
                    class="pl-8"
                    @keyup.enter="handleSearch"
                  />
                </div>
                <Button @click="handleSearch" size="sm">Search</Button>
              </div>
            </CardContent>
          </Card>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <Card v-for="domain in domainPrices" :key="domain.id" 
                class="hover:shadow-md transition-all cursor-pointer group"
                @click="orderDomain(domain.id)">
            <CardContent class="pt-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                  <div class="text-2xl font-bold text-blue-600">.{{ domain.extension }}</div>
                  <Badge v-if="isPremium(domain.extension)" variant="secondary" class="text-xs">
                    <Crown class="h-3 w-3 mr-1" />
                    Premium
                  </Badge>
                </div>
              </div>

              <div class="space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-muted-foreground">Registration</span>
                  <span class="font-semibold">{{ formatPrice(domain.selling_price) }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                  <span class="text-sm text-muted-foreground">Renewal</span>
                  <span class="font-semibold">{{ formatPrice(domain.renewal_price_with_tax) }}</span>
                </div>

                <Button class="w-full mt-4 group-hover:bg-blue-600 transition-colors">
                  <ShoppingCart class="h-4 w-4 mr-2" />
                  Register
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- Why Choose Our Domains -->
      <Card class="bg-gradient-to-br from-blue-50 to-indigo-100">
        <CardContent class="pt-8 pb-8">
          <div class="text-center space-y-6">
            <h2 class="text-2xl font-bold">Why Choose Our Domain Registration?</h2>
            
            <div class="grid gap-6 md:grid-cols-3 max-w-4xl mx-auto">
              <div class="text-center space-y-3">
                <div class="p-3 bg-blue-100 rounded-full w-fit mx-auto">
                  <Globe class="h-6 w-6 text-blue-600" />
                </div>
                <h3 class="font-semibold">Easy Management</h3>
                <p class="text-sm text-muted-foreground">
                  Intuitive control panel to manage all your domains in one place
                </p>
              </div>
              
              <div class="text-center space-y-3">
                <div class="p-3 bg-green-100 rounded-full w-fit mx-auto">
                  <TrendingUp class="h-6 w-6 text-green-600" />
                </div>
                <h3 class="font-semibold">Competitive Pricing</h3>
                <p class="text-sm text-muted-foreground">
                  Best prices in the market with transparent renewal rates
                </p>
              </div>
              
              <div class="text-center space-y-3">
                <div class="p-3 bg-purple-100 rounded-full w-fit mx-auto">
                  <Star class="h-6 w-6 text-purple-600" />
                </div>
                <h3 class="font-semibold">24/7 Support</h3>
                <p class="text-sm text-muted-foreground">
                  Expert support team ready to help with domain issues
                </p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Empty State -->
      <div v-if="domainPrices.length === 0" class="text-center py-12">
        <Globe class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
        <h3 class="text-lg font-semibold mb-2">No domain extensions found</h3>
        <p class="text-muted-foreground">
          {{ search ? 'Try adjusting your search criteria.' : 'No domain extensions are currently available.' }}
        </p>
      </div>
    </div>
  </CustomerLayout>
</template>