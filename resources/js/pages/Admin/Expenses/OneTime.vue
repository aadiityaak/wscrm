<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPrice } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { CheckCircle, CreditCard, DollarSign, Plus, TrendingUp } from 'lucide-vue-next';

interface Expense {
    id: number;
    name: string;
    amount: number;
    currency: string;
    provider: string;
    category: string;
    paid_date: string;
    status: 'paid' | 'pending' | 'cancelled';
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
    { title: 'Sekali Bayar', href: '/admin/expenses/one-time' },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'paid':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'cancelled':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};

const totalOneTime = props.expenses
    .filter(expense => expense.status === 'paid')
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

const currentYear = new Date().getFullYear();
const thisYearExpenses = props.expenses.filter(expense => {
    const expenseYear = new Date(expense.paid_date).getFullYear();
    return expenseYear === currentYear && expense.status === 'paid';
});

const thisYearTotal = thisYearExpenses.reduce((total, expense) => {
    if (expense.currency === 'USD') {
        return total + (expense.amount * 15000); // Convert to IDR
    }
    return total + expense.amount;
}, 0);
</script>

<template>
    <Head title="Pengeluaran Sekali Bayar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ props.title }}</h1>
                    <p class="text-muted-foreground">Kelola pengeluaran sekali bayar seperti deposit domain dan setup infrastruktur</p>
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
                        <CardTitle class="text-sm font-medium">Total Keseluruhan</CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatPrice(totalOneTime, 'IDR') }}</div>
                        <p class="text-xs text-muted-foreground">
                            Dari {{ props.expenses.filter(e => e.status === 'paid').length }} transaksi
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Tahun {{ currentYear }}</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatPrice(thisYearTotal, 'IDR') }}</div>
                        <p class="text-xs text-muted-foreground">
                            Dari {{ thisYearExpenses.length }} transaksi tahun ini
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
                            Jenis pengeluaran
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Sudah Dibayar</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.expenses.filter(e => e.status === 'paid').length }}</div>
                        <p class="text-xs text-muted-foreground">
                            Dari {{ props.expenses.length }} total transaksi
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Expenses Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Daftar Pengeluaran Sekali Bayar</CardTitle>
                    <CardDescription>
                        Riwayat semua pengeluaran yang dibayar sekali untuk investasi dan setup
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama Pengeluaran</TableHead>
                                <TableHead>Provider</TableHead>
                                <TableHead>Kategori</TableHead>
                                <TableHead>Biaya</TableHead>
                                <TableHead>Tanggal Pembayaran</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="expense in props.expenses" :key="expense.id">
                                <TableCell class="font-medium">{{ expense.name }}</TableCell>
                                <TableCell>{{ expense.provider }}</TableCell>
                                <TableCell>{{ expense.category }}</TableCell>
                                <TableCell>{{ formatPrice(expense.amount, expense.currency) }}</TableCell>
                                <TableCell>{{ formatDate(expense.paid_date) }}</TableCell>
                                <TableCell>
                                    <Badge :class="getStatusColor(expense.status)">
                                        {{ expense.status === 'paid' ? 'Dibayar' : expense.status === 'pending' ? 'Pending' : 'Dibatalkan' }}
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