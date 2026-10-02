import { Link, usePage } from '@inertiajs/react';
import {
    BarChart3, Box, ChevronDown, ClipboardList, LayoutDashboard,
    LogOut, Package, Settings, ShoppingCart, Truck, Users, Wrench,
} from 'lucide-react';
import { PropsWithChildren, useState } from 'react';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu, DropdownMenuContent, DropdownMenuItem,
    DropdownMenuSeparator, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';

type NavItem = { label: string; href: string; icon: React.ReactNode; permission?: string };

const navGroups = [
    {
        label: 'Utama',
        items: [
            { label: 'Dashboard', href: '/dashboard', icon: <LayoutDashboard size={16} /> },
            { label: 'Work Order', href: '/work-order', icon: <Wrench size={16} />, permission: 'wo.view' },
        ],
    },
    {
        label: 'Inventori',
        items: [
            { label: 'Barang', href: '/barang', icon: <Box size={16} />, permission: 'barang.view' },
            { label: 'Pembelian', href: '/pembelian', icon: <ShoppingCart size={16} />, permission: 'pembelian.view' },
            { label: 'Mutasi Stok', href: '/stok/mutasi', icon: <Package size={16} />, permission: 'stok.mutasi' },
            { label: 'Stock Opname', href: '/opname', icon: <ClipboardList size={16} />, permission: 'opname.view' },
        ],
    },
    {
        label: 'Data',
        items: [
            { label: 'Pelanggan', href: '/pelanggan', icon: <Users size={16} />, permission: 'pelanggan.view' },
            { label: 'Supplier', href: '/supplier', icon: <Truck size={16} /> },
        ],
    },
    {
        label: 'Laporan',
        items: [
            { label: 'Penjualan', href: '/laporan/penjualan', icon: <BarChart3 size={16} />, permission: 'laporan.view' },
            { label: 'Pembelian', href: '/laporan/pembelian', icon: <BarChart3 size={16} />, permission: 'laporan.view' },
            { label: 'Stok Minimum', href: '/laporan/stok-minimum', icon: <BarChart3 size={16} />, permission: 'laporan.view' },
            { label: 'Nilai Persediaan', href: '/laporan/nilai-persediaan', icon: <BarChart3 size={16} />, permission: 'laporan.view' },
        ],
    },
    {
        label: 'Master',
        items: [
            { label: 'Kategori', href: '/master/kategori', icon: <Settings size={16} />, permission: 'master.view' },
            { label: 'Lokasi Rak', href: '/master/rak', icon: <Settings size={16} />, permission: 'master.view' },
            { label: 'Tarif Jasa', href: '/master/tarif-jasa', icon: <Settings size={16} />, permission: 'master.view' },
            { label: 'Paket Servis', href: '/master/paket-servis', icon: <Settings size={16} />, permission: 'master.view' },
            { label: 'Users', href: '/users', icon: <Users size={16} />, permission: 'user.manage' },
        ],
    },
];

function NavItemLink({ item, permissions }: { item: NavItem; permissions: string[] }) {
    if (item.permission && !permissions.includes(item.permission)) return null;
    const active = window.location.pathname.startsWith(item.href) && item.href !== '/dashboard'
        || window.location.pathname === item.href;

    return (
        <Link
            href={item.href}
            className={cn(
                'flex items-center gap-2 rounded-md px-3 py-2 text-sm transition-colors',
                active
                    ? 'bg-primary text-primary-foreground'
                    : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
            )}
        >
            {item.icon}
            {item.label}
        </Link>
    );
}

export default function AppLayout({ children }: PropsWithChildren) {
    const { auth, flash } = usePage().props as any;
    const permissions: string[] = auth?.permissions ?? [];
    const [sidebarOpen, setSidebarOpen] = useState(true);

    return (
        <div className="flex h-screen bg-background">
            {/* Sidebar */}
            <aside className={cn('flex flex-col border-r bg-card transition-all duration-200', sidebarOpen ? 'w-56' : 'w-0 overflow-hidden')}>
                <div className="flex h-14 items-center border-b px-4">
                    <Wrench size={20} className="text-primary mr-2" />
                    <span className="font-semibold text-sm">Bengkel MS</span>
                </div>
                <nav className="flex-1 overflow-y-auto p-2 space-y-4">
                    {navGroups.map((group) => {
                        const visibleItems = group.items.filter(
                            (i) => !i.permission || permissions.includes(i.permission),
                        );
                        if (!visibleItems.length) return null;
                        return (
                            <div key={group.label}>
                                <p className="px-3 py-1 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    {group.label}
                                </p>
                                {visibleItems.map((item) => (
                                    <NavItemLink key={item.href} item={item} permissions={permissions} />
                                ))}
                            </div>
                        );
                    })}
                </nav>
            </aside>

            {/* Main */}
            <div className="flex flex-1 flex-col overflow-hidden">
                {/* Topbar */}
                <header className="flex h-14 items-center justify-between border-b bg-card px-4">
                    <Button variant="ghost" size="icon" onClick={() => setSidebarOpen(!sidebarOpen)}>
                        <ChevronDown size={16} className={cn('transition-transform', sidebarOpen ? '-rotate-90' : 'rotate-90')} />
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="flex items-center gap-2 h-9">
                                <Avatar className="h-7 w-7">
                                    <AvatarFallback className="text-xs">{auth?.user?.name?.[0]?.toUpperCase()}</AvatarFallback>
                                </Avatar>
                                <span className="text-sm">{auth?.user?.name}</span>
                                <Badge variant="secondary" className="text-xs capitalize">
                                    {auth?.roles?.[0] ?? 'user'}
                                </Badge>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem asChild>
                                <Link href="/profile">Profile</Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem asChild>
                                <Link href="/logout" method="post" as="button" className="w-full flex items-center gap-2 text-destructive">
                                    <LogOut size={14} /> Logout
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </header>

                {/* Flash */}
                {flash?.success && (
                    <div className="mx-4 mt-3 rounded-md bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-800">
                        {flash.success}
                    </div>
                )}
                {flash?.error && (
                    <div className="mx-4 mt-3 rounded-md bg-red-50 border border-red-200 px-4 py-2 text-sm text-red-800">
                        {flash.error}
                    </div>
                )}

                <main className="flex-1 overflow-y-auto p-6">{children}</main>
            </div>
        </div>
    );
}
