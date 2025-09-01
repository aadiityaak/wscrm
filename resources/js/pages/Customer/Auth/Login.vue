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
  form.post('/customer/login', {
    onFinish: () => {
      form.reset('password');
    },
  });
};
</script>

<template>
  <Head title="Customer Login" />

  <AuthCardLayout>
    <div class="space-y-6 auth-card">
      <!-- Header Section -->
      <div class="text-center space-y-3">
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-foreground">Sign In</h2>
        <p class="text-muted-foreground">
          Enter your credentials to access your account
        </p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="submit" class="space-y-5">
        <!-- Email Field -->
        <div class="space-y-2">
          <Label for="email" class="text-sm font-medium text-foreground">Email Address</Label>
          <div class="relative">
            <Input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="Enter your email"
              required
              class="pl-4 pr-4 py-3 text-base auth-input enhanced-focus animate-delay-100"
              :class="form.errors.email ? 'border-destructive focus:border-destructive focus:ring-destructive/20' : ''"
            />
          </div>
          <InputError class="text-xs" :message="form.errors.email" />
        </div>

        <!-- Password Field -->
        <div class="space-y-2">
          <Label for="password" class="text-sm font-medium text-foreground">Password</Label>
          <div class="relative">
            <Input 
              id="password" 
              v-model="form.password"
              type="password" 
              placeholder="Enter your password"
              required 
              class="pl-4 pr-4 py-3 text-base auth-input enhanced-focus animate-delay-200"
              :class="form.errors.password ? 'border-destructive focus:border-destructive focus:ring-destructive/20' : ''"
            />
          </div>
          <InputError class="text-xs" :message="form.errors.password" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <Checkbox 
              id="remember" 
              v-model:checked="form.remember"
              class="data-[state=checked]:bg-primary data-[state=checked]:border-primary"
            />
            <Label for="remember" class="text-sm text-muted-foreground select-none cursor-pointer">
              Remember me for 30 days
            </Label>
          </div>
        </div>

        <!-- Submit Button -->
        <Button 
          type="submit" 
          class="w-full py-3 text-base font-semibold auth-button bg-primary hover:bg-primary/90 focus:ring-2 focus:ring-primary/20 animate-delay-300" 
          :disabled="form.processing"
        >
          <span v-if="!form.processing">Sign In</span>
          <span v-else class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Signing in...
          </span>
        </Button>
      </form>

      <!-- Divider -->
      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <span class="w-full border-t border-muted" />
        </div>
        <div class="relative flex justify-center text-xs uppercase">
          <span class="bg-card px-4 text-muted-foreground font-medium">New to our platform?</span>
        </div>
      </div>

      <!-- Footer Links -->
      <div class="space-y-4 text-center">
        <div class="text-sm">
          <span class="text-muted-foreground">Don't have an account? </span>
          <TextLink 
            href="/customer/register" 
            class="font-semibold text-primary hover:text-primary/80 transition-colors duration-200"
          >
            Create account
          </TextLink>
        </div>

        <div class="pt-4 border-t border-muted">
          <TextLink 
            href="/hosting" 
            class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Shop
          </TextLink>
        </div>
      </div>
    </div>
  </AuthCardLayout>
</template>