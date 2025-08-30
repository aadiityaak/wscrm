<script setup lang="ts">
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('customer.login'), {
    onFinish: () => {
      form.reset('password');
    },
  });
};
</script>

<template>
  <Head title="Customer Login" />

  <AuthCardLayout>
    <Card class="mx-auto max-w-sm">
      <CardHeader>
        <CardTitle class="text-2xl">Customer Login</CardTitle>
        <CardDescription>
          Enter your email below to login to your customer account
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="grid gap-4">
          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="customer@example.com"
              required
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div class="grid gap-2">
            <div class="flex items-center">
              <Label for="password">Password</Label>
            </div>
            <Input 
              id="password" 
              v-model="form.password"
              type="password" 
              required 
            />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <div class="flex items-center space-x-2">
            <Checkbox 
              id="remember" 
              v-model:checked="form.remember" 
            />
            <Label for="remember" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
              Remember me
            </Label>
          </div>

          <Button type="submit" class="w-full" :disabled="form.processing">
            Login
          </Button>
        </form>

        <div class="mt-4 text-center text-sm">
          Don't have an account?
          <TextLink :href="route('customer.register')" class="underline">
            Register
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