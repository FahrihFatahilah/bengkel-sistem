import AppLayout from '@/Layouts/AppLayout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

export default function BarangForm({ barang, kategori, supplier }: { barang?: any; kategori: any[]; supplier: any[] }) {
    const isEdit = !!barang;
    const { data, setData, post, put, processing, errors } = useForm({
        kode: barang?.kode ?? '',
        nama: barang?.nama ?? '',
        kategori_id: barang?.kategori_id ?? '',
        satuan: barang?.satuan ?? 'pcs',
        harga_beli: barang?.harga_beli ?? '',
        harga_jual: barang?.harga_jual ?? '',
        stok_minimum: barang?.stok_minimum ?? 0,
        supplier_utama_id: barang?.supplier_utama_id ?? '',
        is_active: barang?.is_active ?? true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        isEdit ? put(`/barang/${barang.id}`) : post('/barang');
    };

    return (
        <AppLayout>
            <div className="flex items-center gap-3 mb-6">
                <Button variant="ghost" size="icon" asChild><Link href="/barang"><ArrowLeft size={16} /></Link></Button>
                <h1 className="text-xl font-semibold">{isEdit ? 'Edit Barang' : 'Tambah Barang'}</h1>
            </div>

            <Card className="max-w-2xl">
                <CardHeader><CardTitle className="text-base">Informasi Barang</CardTitle></CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label>Kode / SKU *</Label>
                                <Input value={data.kode} onChange={(e) => setData('kode', e.target.value)} disabled={isEdit} />
                                {errors.kode && <p className="text-xs text-destructive">{errors.kode}</p>}
                            </div>
                            <div className="space-y-1">
                                <Label>Satuan *</Label>
                                <Input value={data.satuan} onChange={(e) => setData('satuan', e.target.value)} placeholder="pcs, botol, set..." />
                            </div>
                        </div>

                        <div className="space-y-1">
                            <Label>Nama Barang *</Label>
                            <Input value={data.nama} onChange={(e) => setData('nama', e.target.value)} />
                            {errors.nama && <p className="text-xs text-destructive">{errors.nama}</p>}
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label>Kategori</Label>
                                <Select value={data.kategori_id} onValueChange={(v) => setData('kategori_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Pilih kategori" /></SelectTrigger>
                                    <SelectContent>
                                        {kategori.map((k) => <SelectItem key={k.id} value={k.id}>{k.nama}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-1">
                                <Label>Supplier Utama</Label>
                                <Select value={data.supplier_utama_id} onValueChange={(v) => setData('supplier_utama_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Pilih supplier" /></SelectTrigger>
                                    <SelectContent>
                                        {supplier.map((s) => <SelectItem key={s.id} value={s.id}>{s.nama}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                            <div className="space-y-1">
                                <Label>Harga Beli *</Label>
                                <Input type="number" value={data.harga_beli} onChange={(e) => setData('harga_beli', e.target.value)} />
                                {errors.harga_beli && <p className="text-xs text-destructive">{errors.harga_beli}</p>}
                            </div>
                            <div className="space-y-1">
                                <Label>Harga Jual *</Label>
                                <Input type="number" value={data.harga_jual} onChange={(e) => setData('harga_jual', e.target.value)} />
                                {errors.harga_jual && <p className="text-xs text-destructive">{errors.harga_jual}</p>}
                            </div>
                            <div className="space-y-1">
                                <Label>Stok Minimum</Label>
                                <Input type="number" value={data.stok_minimum} onChange={(e) => setData('stok_minimum', Number(e.target.value))} />
                            </div>
                        </div>

                        <div className="flex gap-3 pt-2">
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Menyimpan...' : 'Simpan'}
                            </Button>
                            <Button type="button" variant="outline" asChild><Link href="/barang">Batal</Link></Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
