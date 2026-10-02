import AppLayout from '@/Layouts/AppLayout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link } from '@inertiajs/react';
import { AlertTriangle, ClipboardList, Package, Wrench } from 'lucide-react';
import { formatRupiah } from '@/lib/utils';

type Stats = { total_barang: number; stok_kritis: number; wo_proses: number; wo_menunggu_bayar: number };

export default function Dashboard({ stats, stokKritis, antriPembayaran }: {
    stats: Stats;
    stokKritis: any[];
    antriPembayaran: any[];
}) {
    return (
        <AppLayout>
            <h1 className="text-xl font-semibold mb-6">Dashboard</h1>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <Card>
                    <CardHeader className="pb-2"><CardTitle className="text-sm text-muted-foreground flex items-center gap-2"><Package size={14} /> Total Barang</CardTitle></CardHeader>
                    <CardContent><p className="text-2xl font-bold">{stats.total_barang}</p></CardContent>
                </Card>
                <Card>
                    <CardHeader className="pb-2"><CardTitle className="text-sm text-muted-foreground flex items-center gap-2"><AlertTriangle size={14} className="text-orange-500" /> Stok Kritis</CardTitle></CardHeader>
                    <CardContent><p className="text-2xl font-bold text-orange-500">{stats.stok_kritis}</p></CardContent>
                </Card>
                <Card>
                    <CardHeader className="pb-2"><CardTitle className="text-sm text-muted-foreground flex items-center gap-2"><Wrench size={14} /> WO Proses</CardTitle></CardHeader>
                    <CardContent><p className="text-2xl font-bold">{stats.wo_proses}</p></CardContent>
                </Card>
                <Card>
                    <CardHeader className="pb-2"><CardTitle className="text-sm text-muted-foreground flex items-center gap-2"><ClipboardList size={14} className="text-blue-500" /> Menunggu Bayar</CardTitle></CardHeader>
                    <CardContent><p className="text-2xl font-bold text-blue-500">{stats.wo_menunggu_bayar}</p></CardContent>
                </Card>
            </div>

            <div className="grid lg:grid-cols-2 gap-6">
                <Card>
                    <CardHeader><CardTitle className="text-sm">Stok Kritis</CardTitle></CardHeader>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Barang</TableHead>
                                    <TableHead className="text-right">Stok</TableHead>
                                    <TableHead className="text-right">Min</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {stokKritis.length === 0 && (
                                    <TableRow><TableCell colSpan={3} className="text-center text-muted-foreground py-6">Tidak ada stok kritis</TableCell></TableRow>
                                )}
                                {stokKritis.map((b) => (
                                    <TableRow key={b.id}>
                                        <TableCell>
                                            <Link href={`/barang/${b.id}`} className="font-medium hover:underline">{b.nama}</Link>
                                            <p className="text-xs text-muted-foreground">{b.kode}</p>
                                        </TableCell>
                                        <TableCell className="text-right text-orange-500 font-medium">{b.stok}</TableCell>
                                        <TableCell className="text-right text-muted-foreground">{b.stok_minimum}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle className="text-sm">Antrian Pembayaran</CardTitle></CardHeader>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>No. WO</TableHead>
                                    <TableHead>Kendaraan</TableHead>
                                    <TableHead className="text-right">Total</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {antriPembayaran.length === 0 && (
                                    <TableRow><TableCell colSpan={3} className="text-center text-muted-foreground py-6">Tidak ada antrian</TableCell></TableRow>
                                )}
                                {antriPembayaran.map((wo) => (
                                    <TableRow key={wo.id}>
                                        <TableCell>
                                            <Link href={`/work-order/${wo.id}`} className="font-medium hover:underline">{wo.nomor}</Link>
                                        </TableCell>
                                        <TableCell>{wo.kendaraan?.nomor_polisi}</TableCell>
                                        <TableCell className="text-right font-medium">{formatRupiah(wo.total)}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
