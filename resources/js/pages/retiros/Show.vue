<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    CheckCircle2,
    Plus,
    Printer,
    ShieldAlert,
    ShieldCheck,
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
}

interface Retiro {
    id: number;
    referencia: string;
    monto: number;
    canal: string;
    metodo_autenticacion: string;
    estado: string;
    saldo_previo: number;
    saldo_posterior: number;
    notificacion_enviada: boolean;
    observacion: string | null;
    created_at: string;
    cuenta: Cuenta;
    cajero: User | null;
}

const props = defineProps<{
    retiro: Retiro;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Retiros', href: '/retiros' },
    { title: props.retiro.referencia, href: `/retiros/${props.retiro.id}` },
];

const money = (n: number) => `Bs. ${Number(n || 0).toLocaleString('es-BO', { minimumFractionDigits: 2 })}`;

const imprimir = () => {
    window.print();
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Comprobante Retiro: ${retiro.referencia}`" />

        <div class="mx-auto max-w-2xl space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Acciones Superiores -->
            <div class="flex items-center justify-between no-print">
                <Link
                    href="/retiros"
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
                        href="/retiros/create"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 hover:bg-amber-500 px-3.5 py-2 text-xs font-semibold text-white shadow-md shadow-amber-500/20 transition-all cursor-pointer"
                    >
                        <Plus class="h-4 w-4" />
                        Nuevo Retiro
                    </Link>
                </div>
            </div>

            <!-- Ticket / Comprobante de Retiro -->
            <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-8 shadow-2xl backdrop-blur-md relative overflow-hidden print:border-none print:shadow-none print:bg-white print:text-black">
                <div class="flex flex-col items-center text-center pb-6 border-b border-slate-800 print:border-black">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400 mb-3 border border-amber-500/20 print:bg-transparent">
                        <ArrowUpRight class="h-7 w-7" />
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-white print:text-black">
                        BANCO CONTINENTAL S.A.
                    </h2>
                    <span class="text-xs text-slate-400 uppercase tracking-widest mt-1 print:text-gray-600">
                        Comprobante Oficial de Retiro
                    </span>
                    <span class="font-mono text-xs text-amber-400 mt-2 font-bold px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20">
                        {{ retiro.referencia }}
                    </span>
                </div>

                <!-- Monto Retirado -->
                <div class="my-6 text-center py-4 rounded-2xl bg-slate-950/60 border border-slate-800 print:bg-gray-100 print:border-gray-300">
                    <span class="text-xs uppercase tracking-wider text-slate-400 block mb-1">Monto Entregado</span>
                    <span class="font-mono text-4xl font-black text-amber-400 print:text-black">
                        {{ money(retiro.monto) }}
                    </span>
                    <span class="text-xs text-slate-500 block mt-1 capitalize font-medium">
                        Canal: {{ retiro.canal }} • Autenticación: {{ retiro.metodo_autenticacion }}
                    </span>
                </div>

                <!-- Desglose de Operación -->
                <div class="space-y-3 text-xs divide-y divide-slate-800/80 print:divide-gray-300">
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Cuenta Debitada:</span>
                        <span class="font-mono font-bold text-white print:text-black">{{ retiro.cuenta.numero_cuenta }} ({{ retiro.cuenta.tipo }})</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Titular de la Cuenta:</span>
                        <span class="font-semibold text-slate-200 print:text-black">{{ retiro.cuenta.cliente.nombres }} {{ retiro.cuenta.cliente.apellidos }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Documento de Identidad:</span>
                        <span class="font-mono text-slate-300 print:text-black">{{ retiro.cuenta.cliente.numero_documento }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Saldo Anterior:</span>
                        <span class="font-mono text-slate-400 print:text-black">{{ money(retiro.saldo_previo) }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Nuevo Saldo Disponible:</span>
                        <span class="font-mono font-bold text-emerald-400 print:text-black">{{ money(retiro.saldo_posterior) }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Fecha y Hora:</span>
                        <span class="text-slate-300 print:text-black">{{ new Date(retiro.created_at).toLocaleString('es-BO') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Cajero Responsable:</span>
                        <span class="text-slate-300 print:text-black">{{ retiro.cajero?.name || 'ATM Terminal' }}</span>
                    </div>
                </div>

                <!-- Notificación de seguridad -->
                <div v-if="retiro.notificacion_enviada" class="mt-4 rounded-xl bg-amber-500/10 border border-amber-500/20 p-3 flex items-center gap-2 text-xs text-amber-300">
                    <ShieldAlert class="h-4 w-4 shrink-0" />
                    <span>Notificación de seguridad transaccional emitida al titular (RF-019).</span>
                </div>

                <!-- Pie de Seguridad -->
                <div class="mt-8 pt-6 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-500 print:border-black print:text-gray-600">
                    <div class="flex items-center gap-1.5">
                        <ShieldCheck class="h-4 w-4 text-amber-400" />
                        <span>Transacción Auditada y Firmada Digitalmente</span>
                    </div>
                    <span class="font-mono">RET-OP-{{ retiro.id }}</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
