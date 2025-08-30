<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

interface DomainPrice {
  id: number;
  extension: string;
  register_price: number;
  renew_price: number;
  transfer_price: number;
  is_active: boolean;
}

interface Props {
  domainPrice: DomainPrice;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Domain Prices', href: '/admin/domain-prices' },
  { title: `Edit .${props.domainPrice.extension}`, href: `/admin/domain-prices/${props.domainPrice.id}/edit` },
];

const form = useForm({
  extension: props.domainPrice.extension,
  register_price: props.domainPrice.register_price,
  renew_price: props.domainPrice.renew_price,
  transfer_price: props.domainPrice.transfer_price,
  is_active: props.domainPrice.is_active,
});

const submit = () => {
  form.put(`/admin/domain-prices/${props.domainPrice.id}`);
};
</script>

<template>
  <Head :title="`Edit Domain Price - .${domainPrice.extension}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Edit Domain Price</h1>
          <p class="text-muted-foreground">Update pricing for .{{ domainPrice.extension }}</p>
        </div>
      </div>

      <Card class="max-w-2xl">
        <CardHeader>
          <CardTitle>Domain Price Details</CardTitle>
          <CardDescription>
            Update the pricing information for .{{ domainPrice.extension }}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <Label for="extension">Domain Extension</Label>
              <Input
                id="extension"
                v-model="form.extension"
                placeholder="com"
                :error="form.errors.extension"
                required
              />
              <p class="text-sm text-muted-foreground mt-1">
                Enter without the dot (e.g., "com" not ".com")
              </p>
              <div v-if="form.errors.extension" class="text-sm text-red-600 mt-1">
                {{ form.errors.extension }}
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <Label for="register_price">Registration Price (IDR)</Label>
                <Input
                  id="register_price"
                  v-model="form.register_price"
                  type="number"
                  step="0.01"
                  min="0"
                  :error="form.errors.register_price"
                  required
                />
                <div v-if="form.errors.register_price" class="text-sm text-red-600 mt-1">
                  {{ form.errors.register_price }}
                </div>
              </div>

              <div>
                <Label for="renew_price">Renewal Price (IDR)</Label>
                <Input
                  id="renew_price"
                  v-model="form.renew_price"
                  type="number"
                  step="0.01"
                  min="0"
                  :error="form.errors.renew_price"
                  required
                />
                <div v-if="form.errors.renew_price" class="text-sm text-red-600 mt-1">
                  {{ form.errors.renew_price }}
                </div>
              </div>

              <div>
                <Label for="transfer_price">Transfer Price (IDR)</Label>
                <Input
                  id="transfer_price"
                  v-model="form.transfer_price"
                  type="number"
                  step="0.01"
                  min="0"
                  :error="form.errors.transfer_price"
                  required
                />
                <div v-if="form.errors.transfer_price" class="text-sm text-red-600 mt-1">
                  {{ form.errors.transfer_price }}
                </div>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <Switch
                id="is_active"
                v-model:checked="form.is_active"
              />
              <Label for="is_active">Active</Label>
            </div>

            <div class="flex justify-end space-x-2">
              <Button type="button" variant="outline" as-child>
                <a href="/admin/domain-prices">Cancel</a>
              </Button>
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Updating...' : 'Update Domain Price' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>