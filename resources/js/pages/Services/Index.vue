<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, Zap, Shield, Palette, ShoppingCart, Settings, Star } from 'lucide-vue-next';

interface ServicePlan {
  id: number;
  name: string;
  category: string;
  description: string;
  price: number;
  features: Record<string, any>;
  is_active: boolean;
}

interface Props {
  servicePlans: Record<string, ServicePlan[]>;
  categories: Record<string, string>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Layanan Kami', href: '/services' },
];

const formatPrice = (price: number): string => {
  if (price === 0) return 'Hubungi Kami';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price);
};

const getCategoryIcon = (category: string) => {
  switch (category) {
    case 'web_package': return Palette;
    case 'addon': return ShoppingCart;
    case 'license': return Shield;
    case 'custom_system': return Settings;
    default: return Star;
  }
};

const getCategoryColor = (category: string) => {
  switch (category) {
    case 'web_package': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
    case 'addon': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    case 'license': return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
    case 'custom_system': return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
  }
};
</script>

<template>
  <Head title="Layanan Kami - WebSweetStudio" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-8 p-6">
      <!-- Hero Section -->
      <div class="text-center space-y-4">
        <h1 class="text-4xl font-bold tracking-tight">Layanan Kami</h1>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
          Pilihan lengkap layanan pengembangan website dan sistem untuk kebutuhan bisnis Anda
        </p>
      </div>

      <!-- Service Categories -->
      <div class="space-y-12">
        <template v-for="(plans, category) in servicePlans" :key="category">
          <div class="space-y-6">
            <div class="flex items-center gap-3">
              <component 
                :is="getCategoryIcon(category)" 
                class="h-8 w-8 text-primary" 
              />
              <div>
                <h2 class="text-2xl font-bold">{{ categories[category] || category }}</h2>
                <p class="text-muted-foreground">
                  {{ category === 'web_package' ? 'Paket website siap pakai dengan berbagai fitur' :
                     category === 'addon' ? 'Tambahan fitur untuk meningkatkan website Anda' :
                     category === 'license' ? 'Lisensi premium untuk tools dan plugin terbaik' :
                     'Solusi custom sesuai kebutuhan spesifik Anda' }}
                </p>
              </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
              <Card 
                v-for="plan in plans" 
                :key="plan.id"
                class="relative overflow-hidden hover:shadow-lg transition-shadow duration-200"
              >
                <CardHeader class="pb-4">
                  <div class="flex items-start justify-between">
                    <div class="space-y-2">
                      <CardTitle class="text-xl">{{ plan.name }}</CardTitle>
                      <Badge :class="getCategoryColor(plan.category)" class="text-xs">
                        {{ categories[plan.category] || plan.category }}
                      </Badge>
                    </div>
                    <div class="text-right">
                      <div class="text-2xl font-bold text-primary">
                        {{ formatPrice(plan.price) }}
                      </div>
                      <div v-if="plan.price > 0" class="text-sm text-muted-foreground">
                        per project
                      </div>
                    </div>
                  </div>
                </CardHeader>

                <CardContent class="space-y-4">
                  <CardDescription class="text-sm leading-relaxed">
                    {{ plan.description }}
                  </CardDescription>

                  <!-- Features -->
                  <div v-if="plan.features && Object.keys(plan.features).length > 0" class="space-y-2">
                    <h4 class="text-sm font-medium">Fitur Unggulan:</h4>
                    <ul class="space-y-1">
                      <li 
                        v-for="(value, feature) in plan.features" 
                        :key="feature"
                        v-if="value"
                        class="flex items-center gap-2 text-sm text-muted-foreground"
                      >
                        <CheckCircle class="h-4 w-4 text-green-500 flex-shrink-0" />
                        <span>{{ 
                          feature === 'responsive_design' ? 'Desain Responsif' :
                          feature === 'seo_optimized' ? 'SEO Optimized' :
                          feature === 'contact_form' ? 'Form Kontak' :
                          feature === 'admin_panel' ? 'Panel Admin' :
                          feature === 'instant_setup' ? 'Setup Instan' :
                          feature === 'pre_built_templates' ? 'Template Siap Pakai' :
                          feature === 'custom_design' ? 'Desain Custom' :
                          feature === 'unlimited_revisions' ? 'Revisi Unlimited' :
                          feature === 'advanced_seo' ? 'SEO Advanced' :
                          feature === 'shopping_cart' ? 'Keranjang Belanja' :
                          feature === 'payment_integration' ? 'Integrasi Payment' :
                          feature === 'inventory_management' ? 'Manajemen Stok' :
                          feature === 'order_tracking' ? 'Tracking Pesanan' :
                          feature === 'custom_development' ? 'Pengembangan Custom' :
                          feature === 'consultation' ? 'Konsultasi' :
                          feature === 'maintenance' ? 'Maintenance' :
                          feature === 'documentation' ? 'Dokumentasi' :
                          feature === 'elementor_pro' ? 'Elementor Pro' :
                          feature === 'wp_rocket' ? 'WP Rocket' :
                          feature === 'crocoblock_suite' ? 'Crocoblock Suite' :
                          feature === 'jetengine' ? 'JetEngine' :
                          feature === 'jetwoobuilder' ? 'JetWooBuilder' :
                          feature === '1_year_license' ? 'Lisensi 1 Tahun' :
                          feature === 'support_included' ? 'Support Included' :
                          feature === 'gpl_license' ? 'Lisensi GPL' :
                          feature === 'regular_updates' ? 'Update Berkala' :
                          feature === 'basic_support' ? 'Support Dasar' :
                          feature.replace(/_/g, ' ')
                        }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="pt-4 space-y-2">
                    <Button asChild class="w-full">
                      <Link :href="`/services/${plan.id}`">
                        {{ plan.price === 0 ? 'Konsultasi Gratis' : 'Pilih Paket' }}
                      </Link>
                    </Button>
                    <Button variant="outline" asChild class="w-full">
                      <Link :href="`/services/${plan.id}`">
                        Lihat Detail
                      </Link>
                    </Button>
                  </div>
                </CardContent>

                <!-- Popular badge for featured plans -->
                <div v-if="plan.category === 'web_package' && plan.name.includes('Custom')" 
                     class="absolute top-4 right-4">
                  <Badge class="bg-primary text-primary-foreground">
                    <Star class="h-3 w-3 mr-1" />
                    Populer
                  </Badge>
                </div>
              </Card>
            </div>
          </div>
        </template>
      </div>

      <!-- CTA Section -->
      <div class="bg-muted/30 rounded-xl p-8 text-center space-y-4">
        <h3 class="text-2xl font-bold">Butuh Solusi Khusus?</h3>
        <p class="text-muted-foreground max-w-2xl mx-auto">
          Tim ahli kami siap membantu Anda mengembangkan solusi digital yang tepat untuk kebutuhan bisnis Anda
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Button size="lg" asChild>
            <Link href="/contact">
              <Zap class="h-4 w-4 mr-2" />
              Konsultasi Gratis
            </Link>
          </Button>
          <Button variant="outline" size="lg" asChild>
            <Link href="/portfolio">
              Lihat Portfolio
            </Link>
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>