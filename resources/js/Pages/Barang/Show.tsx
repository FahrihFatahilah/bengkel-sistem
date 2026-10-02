import AppLayout from '@/Layouts/AppLayout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/react';
import { ArrowLeft, Edit } from 'lucide-react';
import { formatRupiah, formatDateTime } from '@/lib/utils';

const tipeLabel: Record<string, { label: string; color: string }> = {
    masuk: { label: 'Masuk', color: 'text-green-600' },
    keluar: { label: 'Keluar', color: 'text-red-600' },
    mutasi_masuk: { label: 'Mutasi Masuk', color: 'text-blue-600' },
    mutasi_keluar: { label: 'Mutasi Keluar', color: 'text-orange-600' },
    koreksi: { label: 'Koreksi', color: 'text-purple-600' },
    opname: { label: 'Opname', color: 'text-gray-600' },
};

export default function BarangShow({ barang }: { barang: any }) {
    return (
        <AppLayout>
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/barang"><ArrowLeft size={16} /></Link></Button>
                <h1 className="text-xl font-semibold">{barang.nama}</h1>
                <Button variant="outline" size="sm" asChild><Link href={`/barang/${barang.id}/edit`}><Edit size={14} className="mr-1" /> Edit</Link></Button>
            </div>

            <div className="grid lg:grid-cols-3 gap-6 mb-6">
                <Card>
                    <CardHeader><CardTitle className="text-sm">Info Barang</CardTitle></CardHeader>
                    <CardContent className="space-y-2 text-sm">
                        <div className="flex justify-between"><span className="text-muted-foreground">Kode</span><span className="font-mono">{barang.kode}</span></div>
                        <div className="flex justify-between"><span className="text-muted-foreground">Kategori</span><span>{barang.kategori?.nama ?? '-'}</span></div>
                        <div className="flex justify-between"><span className="text-muted-foreground">Satuan</span><span>{barang.satuan}</span></div>
                        <div className="flex justify-between"><span className="text-muted-foreground">Supplier</span><span>{barang.supplier_utama?.nama ?? '-'}</span></div>
                        <div className="flex justify-between"><span className="text-muted-foreground">Status</span><Badge variant={barang.is_active ? 'default' : 'secondary'}>{barang.is_active ? 'Aktif' : 'Nonaktif'}</Badge></div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle className="text-sm">Harga</CardTitle></CardHeader>
                    <CardContent className="space-y-2 text-sm">
                        <div className="flex justify-between"><span className="text-muted-foreground">Harga Beli</span><span>{formatRupiah(barang.harga_beli)}</span></div>
                        <div className="flex justify-between"><span className="text-muted-foreground">Harga Jual</span><span className="font-semibold">{formatRupiah(barang.harga_jual)}</span></div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle className="text-sm">Stok</CardTitle></CardHeader>
                    <CardContent className="space-y-2 text-sm">
                        <div className="flex justify-between"><span className="text-muted-foreground">Stok Saat Ini</span>
                            <span className={`font-bold text-lg ${barang.stok <= barang.stok_minimum ? 'text-orange-500' : ''}`}>{barang.stok} {barang.satuan}</span>
                        </div>
                        <div className="flex justify-between"><span className="text-muted-foreground">Stok Minimum</span><span>{barang.stok_minimum}</span></div>
                        <div className="flex justify-between"><span className="text-muted-foreground">Nilai Stok</span><span>{formatRupiah(barang.stok * barang.harga_beli)}</span></div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <CardTitle className="text-sm">Kartu Stok</CardTitle>
                    <Button variant="outline" size="sm" asChild>
                        <Link href={`/barang/${barang.id}/kartu-stok`}>Lihat Semua</Link>
                    </Button>
                </CardHeader>
                <CardContent className="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Tipe</TableHead>
                                <TableHead>Oleh</TableHead>
                                <TableHead className="text-right">Qty</TableHead>
                                <TableHead className="text-right">Saldo</TableHead>
                                <TableHead>Catatan</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {barang.stok_transaksi?.map((t: any) => {
                                const tipe = tipeLabel[t.tipe] ?? { label: t.tipe, color: '' };
                                return (
                                    <TableRow key={t.id}>
                                        <TableCell className="text-xs">{formatDateTime(t.created_at)}</TableCell>
                                        <TableCell><span className={`text-xs font-medium ${tipe.color}`}>{tipe.label}</span></TableCell>
                                        <TableCell className="text-xs">{t.user?.name}</TableCell>
                                        <TableCell className={`text-right text-xs font-medium ${t.qty > 0 ? 'text-green-600' : 'text-red-600'}`}>
                                            {t.qty > 0 ? '+' : ''}{t.qty}
                                        </TableCell>
                                        <TableCell className="text-right text-xs font-bold">{t.saldo_sesudah}</TableCell>
                                        <TableCell className="text-xs text-muted-foreground">{t.catatan ?? '-'}</TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
