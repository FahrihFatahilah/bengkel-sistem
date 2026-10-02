import AppLayout from '@/Layouts/AppLayout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link, router } from '@inertiajs/react';
import { AlertTriangle, Plus, Search } from 'lucide-react';
import { useState } from 'react';
import { formatRupiah } from '@/lib/utils';

export default function BarangIndex({ barang, filters }: { barang: any; filters: any }) {
    const [search, setSearch] = useState(filters.search ?? '');

    const doSearch = () => router.get('/barang', { search }, { preserveState: true });

    return (
        <AppLayout>
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-xl font-semibold">Barang / Sparepart</h1>
                <Button asChild size="sm">
                    <Link href="/barang/create"><Plus size={14} className="mr-1" /> Tambah Barang</Link>
                </Button>
            </div>

            <div className="flex gap-2 mb-4">
                <Input placeholder="Cari kode / nama..." value={search} onChange={(e) => setSearch(e.target.value)}
                    onKeyDown={(e) => e.key === 'Enter' && doSearch()} className="max-w-xs" />
                <Button variant="outline" size="icon" onClick={doSearch}><Search size={14} /></Button>
            </div>

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Kode</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>Kategori</TableHead>
                            <TableHead className="text-right">Stok</TableHead>
                            <TableHead className="text-right">Harga Jual</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead />
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {barang.data.map((b: any) => (
                            <TableRow key={b.id}>
                                <TableCell className="font-mono text-xs">{b.kode}</TableCell>
                                <TableCell className="font-medium">{b.nama}</TableCell>
                                <TableCell className="text-muted-foreground text-sm">{b.kategori?.nama ?? '-'}</TableCell>
                                <TableCell className="text-right">
                                    <span className={b.stok <= b.stok_minimum ? 'text-orange-500 font-medium' : ''}>
                                        {b.stok} {b.stok <= b.stok_minimum && <AlertTriangle size={12} className="inline ml-1" />}
                                    </span>
                                </TableCell>
                                <TableCell className="text-right">{formatRupiah(b.harga_jual)}</TableCell>
                                <TableCell>
                                    <Badge variant={b.is_active ? 'default' : 'secondary'}>{b.is_active ? 'Aktif' : 'Nonaktif'}</Badge>
                                </TableCell>
                                <TableCell>
                                    <div className="flex gap-1 justify-end">
                                        <Button variant="ghost" size="sm" asChild><Link href={`/barang/${b.id}`}>Detail</Link></Button>
                                        <Button variant="ghost" size="sm" asChild><Link href={`/barang/${b.id}/edit`}>Edit</Link></Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>

            {/* Pagination */}
            <div className="flex items-center justify-between mt-4 text-sm text-muted-foreground">
                <span>Total: {barang.total} barang</span>
                <div className="flex gap-1">
                    {barang.links.map((link: any, i: number) => (
                        <Button key={i} variant={link.active ? 'default' : 'outline'} size="sm"
                            disabled={!link.url}
                            onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                            dangerouslySetInnerHTML={{ __html: link.label }} />
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
