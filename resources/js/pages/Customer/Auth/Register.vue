<script setup lang="ts">
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  city: '',
  country: 'Indonesia',
  postal_code: '',
});

const submit = () => {
  form.post(route('customer.register'));
};
</script>

<template>
  <Head title="Customer Register" />

  <AuthCardLayout>
    <Card class="mx-auto max-w-md">
      <CardHeader>
        <CardTitle class="text-2xl">Create Customer Account</CardTitle>
        <CardDescription>
          Enter your information below to create your account
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="grid gap-4">
          <div class="grid gap-2">
            <Label for="name">Full Name</Label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="John Doe"
              required
            />
            <InputError class="mt-1" :message="form.errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="john@example.com"
              required
            />
            <InputError class="mt-1" :message="form.errors.email" />
          </div>

          <div class="grid gap-2">
            <Label for="phone">Phone Number</Label>
            <Input
              id="phone"
              v-model="form.phone"
              type="tel"
              placeholder="+62812345678"
            />
            <InputError class="mt-1" :message="form.errors.phone" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <Label for="password">Password</Label>
              <Input 
                id="password" 
                v-model="form.password"
                type="password" 
                required 
              />
              <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
              <Label for="password_confirmation">Confirm Password</Label>
              <Input 
                id="password_confirmation" 
                v-model="form.password_confirmation"
                type="password" 
                required 
              />
            </div>
          </div>

          <div class="grid gap-2">
            <Label for="address">Address</Label>
            <Input
              id="address"
              v-model="form.address"
              type="text"
              placeholder="Street address"
            />
            <InputError class="mt-1" :message="form.errors.address" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <Label for="city">City</Label>
              <Input
                id="city"
                v-model="form.city"
                type="text"
                placeholder="Jakarta"
              />
              <InputError class="mt-1" :message="form.errors.city" />
            </div>

            <div class="grid gap-2">
              <Label for="postal_code">Postal Code</Label>
              <Input
                id="postal_code"
                v-model="form.postal_code"
                type="text"
                placeholder="12345"
              />
              <InputError class="mt-1" :message="form.errors.postal_code" />
            </div>
          </div>

          <Button type="submit" class="w-full" :disabled="form.processing">
            Create Account
          </Button>
        </form>

        <div class="mt-4 text-center text-sm">
          Already have an account?
          <TextLink :href="route('customer.login')" class="underline">
            Login
          </TextLink>
        </div>

        <div class="mt-2 text-center text-sm">
          <TextLink href="/hosting" class="underline">
            ← Back to Shop
          </TextLink>
        </div>
      </CardContent>
    </Card>
  </AuthCardLayout>
</template>