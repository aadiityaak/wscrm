<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPrice } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Calendar, CreditCard, DollarSign, Plus, TrendingUp } from 'lucide-vue-next';

interface Expense {
    id: number;
    name: string;
    amount: number;
    currency: string;
    provider: string;
    category: string;
    next_billing: string;
    status: 'active' | 'inactive' | 'pending';
}

interface Props {
    title: string;
    expenses: Expense[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Financial', href: '#' },
    { title: 'Data Pengeluaran', href: '#' },
    { title: 'Tahunan', href: '/admin/expenses/yearly' },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'inactive':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};

const totalYearly = props.expenses
    .filter(expense => expense.status === 'active')
    .reduce((total, expense) => {
        if (expense.currency === 'USD') {
            return total + (expense.amount * 15000); // Convert to IDR
        }
        return total + expense.amount;
    }, 0);

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Pengeluaran Tahunan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ props.title }}</h1>
                    <p class="text-muted-foreground">Kelola pengeluaran tahunan seperti lisensi domain dan software</p>
                </div>
                <Button class="cursor-pointer">
                    <Plus class="mr-2 h-4 w-4" />
                    Tambah Pengeluaran
                </Button>
            </div>

            <!-- Summary Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Tahunan</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatPrice(totalYearly, 'IDR') }}</div>
                        <p class="text-xs text-muted-foreground">
                            Dari {{ props.expenses.filter(e => e.status === 'active').length }} layanan aktif
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Layanan Aktif</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.expenses.filter(e => e.status === 'active').length }}</div>
                        <p class="text-xs text-muted-foreground">
                            Dari {{ props.expenses.length }} total layanan
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Kategori</CardTitle>
                        <CreditCard class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ new Set(props.expenses.map(e => e.category)).size }}</div>
                        <p class="text-xs text-muted-foreground">
                            Kategori pengeluaran
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Jatuh Tempo</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ props.expenses.filter(e => {
                                const nextBilling = new Date(e.next_billing);
                                const today = new Date();
                                const diffTime = nextBilling.getTime() - today.getTime();
                                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                return diffDays <= 30;
                            }).length }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Dalam 30 hari ke depan
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Expenses Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Daftar Pengeluaran Tahunan</CardTitle>
                    <CardDescription>
                        Kelola semua pengeluaran tahunan untuk lisensi dan layanan jangka panjang
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama Layanan</TableHead>
                                <TableHead>Provider</TableHead>
                                <TableHead>Kategori</TableHead>
                                <TableHead>Biaya</TableHead>
                                <TableHead>Renewal Berikutnya</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="expense in props.expenses" :key="expense.id">
                                <TableCell class="font-medium">{{ expense.name }}</TableCell>
                                <TableCell>{{ expense.provider }}</TableCell>
                                <TableCell>{{ expense.category }}</TableCell>
                                <TableCell>{{ formatPrice(expense.amount, expense.currency) }}</TableCell>
                                <TableCell>{{ formatDate(expense.next_billing) }}</TableCell>
                                <TableCell>
                                    <Badge :class="getStatusColor(expense.status)">
                                        {{ expense.status === 'active' ? 'Aktif' : expense.status === 'inactive' ? 'Tidak Aktif' : 'Pending' }}
                                    </Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>