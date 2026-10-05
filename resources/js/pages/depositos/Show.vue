<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowDownLeft,
    CheckCircle2,
    Clock,
    Plus,
    Printer,
    ShieldCheck,
    Wallet,
} from 'lucide-vue-next';

interface Cliente {
    nombres: string;
    apellidos: string;
    numero_documento: string;
}

interface Cuenta {
    numero_cuenta: string;
    tipo: string;
    cliente: Cliente;
}

interface User {
    name: string;
    username: string;
}

interface Deposito {
    id: number;
    referencia: string;
    tipo: string;
    monto: number;
    estado: string;
    canal: string;
    banco_emisor: string | null;
    numero_cheque: string | null;
    observacion: string | null;
    created_at: string;
    cuenta: Cuenta;
    cajero: User | null;
}

const props = defineProps<{
    deposito: Deposito;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Depósitos', href: '/depositos' },
    { title: props.deposito.referencia, href: `/depositos/${props.deposito.id}` },
];

const money = (n: number) => `Bs. ${Number(n || 0).toLocaleString('es-BO', { minimumFractionDigits: 2 })}`;

const imprimir = () => {
    window.print();
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Comprobante: ${deposito.referencia}`" />

        <div class="mx-auto max-w-2xl space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Acciones Superiores -->
            <div class="flex items-center justify-between no-print">
                <Link
                    href="/depositos"
                    class="text-xs font-semibold text-slate-400 hover:text-white"
                >
                    ← Volver al listado
                </Link>
                <div class="flex gap-2">
                    <button
                        @click="imprimir"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 px-3.5 py-2 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer"
                    >
                        <Printer class="h-4 w-4" />
                        Imprimir Recibo
                    </button>
                    <Link
                        href="/depositos/create"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-3.5 py-2 text-xs font-semibold text-white shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                    >
                        <Plus class="h-4 w-4" />
                        Nuevo Depósito
                    </Link>
                </div>
            </div>

            <!-- Ticket / Comprobante de Depósito (RF-013) -->
            <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-8 shadow-2xl backdrop-blur-md relative overflow-hidden print:border-none print:shadow-none print:bg-white print:text-black">
                <!-- Watermark / Banner -->
                <div class="flex flex-col items-center text-center pb-6 border-b border-slate-800 print:border-black">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-400 mb-3 border border-emerald-500/20 print:bg-transparent">
                        <CheckCircle2 class="h-7 w-7" />
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-white print:text-black">
                        BANCO CONTINENTAL S.A.
                    </h2>
                    <span class="text-xs text-slate-400 uppercase tracking-widest mt-1 print:text-gray-600">
                        Comprobante Digital de Depósito
                    </span>
                    <span class="font-mono text-xs text-emerald-400 mt-2 font-bold px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                        {{ deposito.referencia }}
                    </span>
                </div>

                <!-- Monto Destacado -->
                <div class="my-6 text-center py-4 rounded-2xl bg-slate-950/60 border border-slate-800 print:bg-gray-100 print:border-gray-300">
                    <span class="text-xs uppercase tracking-wider text-slate-400 block mb-1">Monto Depositado</span>
                    <span class="font-mono text-4xl font-black text-emerald-400 print:text-black">
                        {{ money(deposito.monto) }}
                    </span>
                    <span class="text-xs text-slate-500 block mt-1 capitalize font-medium">
                        Modalidad: {{ deposito.tipo }} • Estado: {{ deposito.estado }}
                    </span>
                </div>

                <!-- Desglose de Operación -->
                <div class="space-y-3 text-xs divide-y divide-slate-800/80 print:divide-gray-300">
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Cuenta de Destino:</span>
                        <span class="font-mono font-bold text-white print:text-black">{{ deposito.cuenta.numero_cuenta }} ({{ deposito.cuenta.tipo }})</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Titular de la Cuenta:</span>
                        <span class="font-semibold text-slate-200 print:text-black">{{ deposito.cuenta.cliente.nombres }} {{ deposito.cuenta.cliente.apellidos }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Documento de Identidad:</span>
                        <span class="font-mono text-slate-300 print:text-black">{{ deposito.cuenta.cliente.numero_documento }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Canal de Operación:</span>
                        <span class="capitalize text-slate-300 print:text-black">{{ deposito.canal }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Fecha y Hora:</span>
                        <span class="text-slate-300 print:text-black">{{ new Date(deposito.created_at).toLocaleString('es-BO') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Cajero / Operador:</span>
                        <span class="text-slate-300 print:text-black">{{ deposito.cajero?.name || 'Sistema Central' }}</span>
                    </div>
                    <div v-if="deposito.banco_emisor" class="flex justify-between py-2">
                        <span class="text-slate-400">Banco Cheque / N°:</span>
                        <span class="text-amber-400 font-semibold print:text-black">{{ deposito.banco_emisor }} (#{{ deposito.numero_cheque }})</span>
                    </div>
                </div>

                <!-- Pie de Seguridad -->
                <div class="mt-8 pt-6 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-500 print:border-black print:text-gray-600">
                    <div class="flex items-center gap-1.5">
                        <ShieldCheck class="h-4 w-4 text-emerald-400" />
                        <span>Transacción Segura Verificada (ACID)</span>
                    </div>
                    <span class="font-mono">EXP-{{ deposito.id }}</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
