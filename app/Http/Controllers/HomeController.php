<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Services\NotifikasiService;
use App\Models\Diskon;
use App\Models\Jurnal;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Pengambilan;
use App\Models\Penjualandetail;
use App\Models\Penjualanheader;
use App\Models\Reservasi;
use Autocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
    public function index()
    {
        return view('home.index');
    }

    public function products()
    {
        $barang = DB::table('persediaan as a')
            ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
            ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as kuantitas FROM pengambilan GROUP BY id_persediaan) as c'), 'a.id', '=', 'c.id_persediaan')
            ->select('a.id_barang as id', 'b.kode_barang', 'b.nama_barang', 'b.harga_satuan', 'b.gambar_barang', DB::raw('IFNULL(SUM(a.kuantitas), 0) - IFNULL(SUM(c.kuantitas), 0) as stok'))
            ->where('b.gambar_barang', '!=', 'dummy-image.png')
            ->havingRaw('IFNULL(SUM(a.kuantitas), 0) - IFNULL(SUM(c.kuantitas), 0) > 0')
            ->groupBy('a.id_barang', 'b.kode_barang', 'b.nama_barang', 'b.harga_satuan', 'b.gambar_barang')
            ->get();

        return view('home.products', ['barang' => $barang]);
    }

    public function cart()
    {
        $cart = session()->get('cart', []);

        // Calculate discounts for each item in cart
        foreach ($cart as $id => &$item) {
            $total = $item['kuantitas'] * $item['harga_satuan'];

            // Get applicable discount
            $diskon = $this->getDiskonForItem($id, $total);

            if ($diskon) {
                $diskon_nominal = ($diskon->persentase_diskon / 100) * $total;
                $subtotal = $total - $diskon_nominal;

                $item['diskon'] = $diskon;
                $item['diskon_nominal'] = $diskon_nominal;
                $item['subtotal'] = $subtotal;
            } else {
                $item['diskon'] = null;
                $item['diskon_nominal'] = 0;
                $item['subtotal'] = $total;
            }
        }

        session()->put('cart', $cart);

        return view('home.cart');
    }


    private function getDiskonForItem($id_barang, $total)
    {
        return Diskon::where('id_barang', $id_barang)
            ->where('min_transaksi', '<=', $total)
            ->orderBy('persentase_diskon', 'desc')
            ->first();
    }

    public function addCart($id)
    {
        $product = DB::table('persediaan as a')
            ->leftJoin('barang as b', 'a.id_barang', '=', 'b.id')
            ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as kuantitas FROM pengambilan GROUP BY id_persediaan) as c'), 'a.id', '=', 'c.id_persediaan')
            ->select('a.id_barang as id', 'b.kode_barang', 'b.nama_barang', 'b.harga_satuan', 'b.gambar_barang', DB::raw('IFNULL(SUM(a.kuantitas), 0) - IFNULL(SUM(c.kuantitas), 0) as stok'))
            ->where('b.gambar_barang', '!=', 'dummy-image.png')
            ->havingRaw('IFNULL(SUM(a.kuantitas), 0) - IFNULL(SUM(c.kuantitas), 0) > 0')
            ->groupBy('a.id_barang', 'b.kode_barang', 'b.nama_barang', 'b.harga_satuan', 'b.gambar_barang')
            ->where('a.id_barang', $id)
            ->first();

        if (!$product) {
            return redirect()->route('home.products');
        }

        $cart = session()->get('cart');

        if (!$cart) {
            $cart = [
                $id => [
                    'kode_barang' => $product->kode_barang,
                    'nama_barang' => $product->nama_barang,
                    'kuantitas' => 1,
                    'stok' => $product->stok - 1,
                    'harga_satuan' => $product->harga_satuan,
                    'gambar_barang' => $product->gambar_barang,
                ],
            ];

            session()->put('cart', $cart);

            return redirect()->route('home.products')->with('success', 'Product added to cart successfully!');
        }

        if (isset($cart[$id])) {
            $cart[$id]['kuantitas']++;
            $cart[$id]['stok']--;
            session()->put('cart', $cart);

            return redirect()->route('home.products')->with('success', 'Product added to cart successfully!');
        }

        $cart[$id] = [
            'kode_barang' => $product->kode_barang,
            'nama_barang' => $product->nama_barang,
            'kuantitas' => 1,
            'stok' => $product->stok - 1,
            'harga_satuan' => $product->harga_satuan,
            'gambar_barang' => $product->gambar_barang,
        ];

        session()->put('cart', $cart);

        return redirect()->route('home.products')->with('success', 'Product added to cart successfully!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('home.cart')->with('success', 'Product removed from cart successfully!');
    }

    public function decrement($id)
    {
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            if ($cart[$id]['kuantitas'] > 1) {
                $cart[$id]['kuantitas']--;
                $cart[$id]['stok']++;

                // Update cart in session
                session()->put('cart', $cart);

                // Recalculate discount
                return $this->cart();
            }
        }

        return redirect()->route('home.cart')->with('error', 'Product not found in cart!');
    }

    public function increment($id)
    {
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            if ($cart[$id]['stok'] > 0) {
                $cart[$id]['kuantitas']++;
                $cart[$id]['stok']--;

                // Update cart in session
                session()->put('cart', $cart);

                // Recalculate discount
                return $this->cart();
            }
        }

        return redirect()->route('home.cart')->with('success', 'Product quantity incremented successfully!');
    }

    public function checkout()
    {
        $cart = session()->get('cart');

        if (!$cart) {
            return redirect()->route('home.products');
        }

        $penjualan_header = Penjualanheader::create([
            'no_penjualan' => Autocode::code('penjualan_header', 'no_penjualan', 'PP'),
            'tgl_penjualan' => now(),
            'keterangan' => 'Pembelian produk dari pelanggan ' . Auth::user()->name,
            'status_pembayaran' => 'belum_lunas',
            'id_pelanggan' => Pelanggan::where('user_id_created', Auth::user()->id)->first()->id,
            'user_id_created' => Auth::user()->id,
            'user_id_updated' => Auth::user()->id,
        ]);

        foreach ($cart as $id => $item) {
            // Get discount information
            $total = $item['kuantitas'] * $item['harga_satuan'];
            $diskon = isset($item['diskon']) ? $item['diskon'] : $this->getDiskonForItem($id, $total);

            if ($diskon) {
                $diskon_nominal = ($diskon->persentase_diskon / 100) * $total;
                $subtotal = $total - $diskon_nominal;
                $id_diskon = $diskon->id;
            } else {
                $diskon_nominal = 0;
                $subtotal = $total;
                $id_diskon = null;
            }

            $penjualan_detail = Penjualandetail::create([
                'kuantitas' => $item['kuantitas'],
                'harga_satuan' => $item['harga_satuan'],
                'diskon' => $diskon_nominal,
                'subtotal' => $subtotal,
                'id_diskon' => $id_diskon,
                'id_barang' => $id,
                'id_penjualan_header' => $penjualan_header->id,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            $result = DB::table('persediaan as a')
                ->select('a.*', DB::raw('COALESCE(b.total_pengambilan, 0) as total_pengambilan'), DB::raw('a.kuantitas - COALESCE(b.total_pengambilan, 0) as saldo_stok'))
                ->leftJoin(DB::raw('(SELECT id_persediaan, SUM(kuantitas) as total_pengambilan FROM pengambilan GROUP BY id_persediaan) b'), 'a.id', '=', 'b.id_persediaan')
                ->where('a.id_barang', $id)
                ->whereRaw('a.kuantitas - COALESCE(b.total_pengambilan, 0) > 0')
                ->orderBy('a.tgl_persediaan')
                ->get();
            $kuantitas_pengambilan = $item['kuantitas'];

            foreach ($result as $r) {
                if ($kuantitas_pengambilan >= $r->saldo_stok && $kuantitas_pengambilan > 0) {
                    Pengambilan::create([
                        'tgl_pengambilan' => now(),
                        'keterangan' => 'Penjualan dengan Nomor Penjualan ' . $penjualan_header->no_penjualan,
                        'kuantitas' => $r->saldo_stok,
                        'id_barang' => $id,
                        'id_persediaan' => $r->id,
                        'id_penjualan_detail' => $penjualan_detail->id,
                        'user_id_created' => Auth::user()->id,
                        'user_id_updated' => Auth::user()->id,
                    ]);
                    $kuantitas_pengambilan -= $r->saldo_stok;
                } else if ($kuantitas_pengambilan > 0) {
                    Pengambilan::create([
                        'tgl_pengambilan' => now(),
                        'keterangan' => 'Penjualan dengan Nomor Penjualan ' . $penjualan_header->no_penjualan,
                        'kuantitas' => $kuantitas_pengambilan,
                        'id_barang' => $id,
                        'id_persediaan' => $r->id,
                        'id_penjualan_detail' => $penjualan_detail->id,
                        'user_id_created' => Auth::user()->id,
                        'user_id_updated' => Auth::user()->id,
                    ]);
                    $kuantitas_pengambilan = 0;
                }
            }
        }

        session()->forget('cart');

        return redirect()->route('home.products')->with('success', 'Checkout successfully!');
    }


    public function getDiskon(Request $request)
    {
        $id_barang = $request->id_barang;
        $total = $request->total;

        $diskon = Diskon::where('id_barang', $id_barang)
            ->where('min_transaksi', '<=', $total)
            ->orderBy('persentase_diskon', 'desc')
            ->first();

        if ($diskon) {
            $diskon_nominal = min(($total * $diskon->persentase_diskon / 100), $diskon->max_diskon);
            return response()->json([
                'success' => true,
                'diskon' => $diskon,
                'diskon_nominal' => $diskon_nominal,
                'subtotal' => $total - $diskon_nominal
            ]);
        }

        return response()->json([
            'success' => false,
            'diskon_nominal' => 0,
            'subtotal' => $total
        ]);
    }

    public function history()
    {
        $history = DB::table('penjualan_header as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->leftJoin(DB::raw("(SELECT x.id_penjualan_header, GROUP_CONCAT(' ',CONCAT(x.kuantitas, ' ', y.nama_barang)) as daftar_barang, SUM(x.kuantitas * x.harga_satuan - x.diskon) as total FROM penjualan_detail as x LEFT JOIN barang as y ON x.id_barang = y.id GROUP BY x.id_penjualan_header) as c"), 'a.id', '=', 'c.id_penjualan_header')
            ->select('a.id', 'a.no_penjualan', 'a.tgl_penjualan', 'b.nama_pelanggan', 'c.daftar_barang', 'a.status_pembayaran', DB::raw('IFNULL(c.total, 0) as total'))
            ->where('a.user_id_created', Auth::user()->id)
            ->get();

        return view('home.history', ['history' => $history]);
    }

    public function getSnapToken($id)
    {
        try {
            // Validasi input
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid transaction ID'], 400);
            }

            // Get transaction data
            $penjualan = DB::table('penjualan_header as a')
                ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
                ->leftJoin(DB::raw("(SELECT x.id_penjualan_header, GROUP_CONCAT(' ',CONCAT(x.kuantitas, ' ', y.nama_barang)) as daftar_barang, SUM(x.subtotal) as total FROM penjualan_detail as x LEFT JOIN barang as y ON x.id_barang = y.id GROUP BY x.id_penjualan_header) as c"), 'a.id', '=', 'c.id_penjualan_header')
                ->select(
                    'a.id',
                    'a.no_penjualan',
                    'a.tgl_penjualan',
                    'b.nama_pelanggan',
                    'c.daftar_barang',
                    'a.status_pembayaran',
                    DB::raw('IFNULL(c.total, 0) as total')
                )
                ->where('a.id', $id)
                ->where('a.user_id_created', Auth::user()->id) // Security check
                ->first();

            if (!$penjualan) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Check if already paid
            if ($penjualan->status_pembayaran === 'lunas') {
                return response()->json(['error' => 'Transaction already paid'], 400);
            }

            // Get transaction details
            $penjualan_detail = DB::table('penjualan_detail as x')
                ->join('barang as y', 'x.id_barang', '=', 'y.id')
                ->select(
                    'y.nama_barang as name',
                    'x.harga_satuan as price',
                    'x.kuantitas as quantity',
                    'x.subtotal as subtotal'
                )
                ->where('x.id_penjualan_header', $id)
                ->get();

            if ($penjualan_detail->isEmpty()) {
                return response()->json(['error' => 'No items found in transaction'], 400);
            }

            // Format item details for Midtrans
            $items = [];
            foreach ($penjualan_detail as $item) {
                $items[] = [
                    'id' => 'item-' . uniqid(),
                    'price' => (int)$item->price,
                    'quantity' => (int)$item->quantity,
                    'name' => substr($item->name, 0, 50), // Limit name length
                ];
            }

            // Create transaction parameters
            $params = [
                'transaction_details' => [
                    'order_id' => $penjualan->no_penjualan,
                    'gross_amount' => (int)$penjualan->total,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'item_details' => $items,
                'callbacks' => [
                    'finish' => route('home.history'),
                ],
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => 60
                ]
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snapToken' => $snapToken,
                'order_id' => $penjualan->no_penjualan,
                'amount' => $penjualan->total
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage(), [
                'transaction_id' => $id,
                'user_id' => Auth::user()->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to generate payment token',
                'message' => 'Please try again later'
            ], 500);
        }
    }

    public function checkStatus()
    {
        try {
            $penjualan = Penjualanheader::where('user_id_created', Auth::user()->id)
                ->where('status_pembayaran', '!=', 'lunas')
                ->get();

            $updatedCount = 0;

            foreach ($penjualan as $p) {
                try {
                    // Use Midtrans API to check status
                    $serverKey = env('MIDTRANS_SERVER_KEY');
                    $isProduction = env('MIDTRANS_IS_PRODUCTION', false);

                    $baseUrl = $isProduction ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
                    $url = $baseUrl . '/v2/' . $p->no_penjualan . '/status';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_USERPWD, $serverKey . ':');
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $output = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if (curl_error($ch)) {
                        Log::error('cURL Error: ' . curl_error($ch));
                        curl_close($ch);
                        continue;
                    }
                    curl_close($ch);

                    if ($httpCode !== 200 || !$output) {
                        continue;
                    }

                    $result = json_decode($output, true);

                    if (!isset($result['transaction_status'])) {
                        continue;
                    }

                    // Check if payment is successful
                    if (in_array($result['transaction_status'], ['settlement', 'capture'])) {
                        // Update payment status
                        Penjualanheader::where('id', $p->id)->update([
                            'status_pembayaran' => 'lunas'
                        ]);
                        $updatedCount++;

                        // Create journal entries
                        $this->createJournalEntries($p->id);
                    }
                } catch (\Exception $e) {
                    Log::error('Status check error for order ' . $p->no_penjualan . ': ' . $e->getMessage());
                    continue;
                }
            }

            $message = $updatedCount > 0
                ? 'Payment status updated! ' . $updatedCount . ' transaction(s) marked as paid.'
                : 'No new payments found.';

            return redirect()->route('home.history')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Check Status Error: ' . $e->getMessage());
            return redirect()->route('home.history')->with('error', 'Failed to check payment status');
        }
    }

    private function createJournalEntries($penjualanHeaderId)
    {
        try {
            $penjualan_detail = Penjualandetail::where('id_penjualan_header', $penjualanHeaderId)->get();

            // Check or create COA accounts
            $coa_kas = Coa::firstOrCreate(
                ['kode_akun' => '101'],
                [
                    'nama_akun' => 'Kas',
                    'header_akun' => 1,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]
            );

            $coa_penjualan = Coa::firstOrCreate(
                ['kode_akun' => '406'],
                [
                    'nama_akun' => 'Penjualan',
                    'header_akun' => 4,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]
            );

            foreach ($penjualan_detail as $pd) {
                // Check if journal already exists
                $existingJurnal = Jurnal::where('jenis_transaksi', 'penjualan')
                    ->where('id_transaksi', $pd->id)
                    ->exists();

                if ($existingJurnal) {
                    continue;
                }

                $jurnalNo = Autocode::code('jurnal', 'no_jurnal', 'JU');

                // Debit entry (Kas)
                Jurnal::create([
                    'no_jurnal' => $jurnalNo,
                    'tgl_jurnal' => now(),
                    'posisi_dr_cr' => 'd',
                    'nominal' => $pd->subtotal,
                    'jenis_transaksi' => 'penjualan',
                    'id_transaksi' => $pd->id,
                    'id_coa' => $coa_kas->id,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);

                // Credit entry (Penjualan)
                Jurnal::create([
                    'no_jurnal' => $jurnalNo,
                    'tgl_jurnal' => now(),
                    'posisi_dr_cr' => 'c',
                    'nominal' => $pd->subtotal,
                    'jenis_transaksi' => 'penjualan',
                    'id_transaksi' => $pd->id,
                    'id_coa' => $coa_penjualan->id,
                    'user_id_created' => Auth::user()->id,
                    'user_id_updated' => Auth::user()->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Journal creation error: ' . $e->getMessage());
        }
    }


    public function reservations()
    {
        $layanan = Layanan::all();
        return view('home.reservations', ['layanan' => $layanan]);
    }

    public function addReservation($id)
    {
        $no_reservasi = Autocode::code('reservasi', 'no_reservasi', 'RT');
        $layanan = Layanan::find($id);

        return view('home.add-reservation', [
            'no_reservasi' => $no_reservasi,
            'layanan' => $layanan
        ]);
    }

    public function storeReservation(Request $request)
    {
        $validated = $request->validate([
            'no_reservasi' => 'required',
            'tgl_reservasi' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'id_layanan' => 'required',
        ], [
            'no_reservasi.required' => 'Nomor Reservasi harus diisi',
            'tgl_reservasi.required' => 'Tanggal Reservasi harus diisi',
            'waktu_mulai.required' => 'Waktu Mulai harus diisi',
            'waktu_selesai.nullable' => 'Waktu Selesai harus diisi',
            'id_layanan.required' => 'Layanan harus diisi',
        ]);

        try {
            $reservasi = Reservasi::create([
                'no_reservasi' => $request->no_reservasi,
                'tgl_reservasi' => $request->tgl_reservasi,
                'status' => 'pending',
                'id_pelanggan' => Pelanggan::where('user_id_created', Auth::user()->id)->first()->id,
                'id_layanan' => $request->id_layanan,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'user_id_created' => Auth::user()->id,
                'user_id_updated' => Auth::user()->id,
            ]);

            // Tambahkan notifikasi setelah reservasi berhasil dibuat
            NotifikasiService::createReservasiNotification($reservasi->id, Auth::user()->id);

            return redirect()->route('home.reservations')->with('success', 'Reservasi berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->route('home.reservations')->with('error', $e->getMessage());
        }
    }
    public function historyReservation()
    {
        $history = DB::table('reservasi as a')
            ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
            ->leftJoin('layanan as d', 'a.id_layanan', '=', 'd.id')
            ->select('a.id', 'a.no_reservasi', 'a.tgl_reservasi', 'b.nama_pelanggan', 'd.nama_layanan', 'a.waktu_mulai', 'a.waktu_selesai', 'a.status', 'a.ruangan')
            ->where('a.user_id_created', Auth::user()->id)
            ->get();

        return view('home.history-reservation', ['history' => $history]);
    }

    public function debugSnapToken($id)
    {
        try {
            // Check environment variables
            $debug = [
                'env_server_key' => env('MIDTRANS_SERVER_KEY') ? 'SET' : 'NOT SET',
                'env_client_key' => env('MIDTRANS_CLIENT_KEY') ? 'SET' : 'NOT SET',
                'env_is_production' => env('MIDTRANS_IS_PRODUCTION', false),
                'transaction_id' => $id,
                'user_id' => Auth::user()->id,
            ];

            // Check if Midtrans classes exist
            if (!class_exists('Midtrans\Config')) {
                $debug['midtrans_error'] = 'Midtrans\Config class not found. Please install midtrans/midtrans-php';
            }

            if (!class_exists('Midtrans\Snap')) {
                $debug['midtrans_error'] = 'Midtrans\Snap class not found. Please install midtrans/midtrans-php';
            }

            // Check transaction exists
            $penjualan = DB::table('penjualan_header as a')
                ->leftJoin('pelanggan as b', 'a.id_pelanggan', '=', 'b.id')
                ->leftJoin(DB::raw("(SELECT x.id_penjualan_header, SUM(x.subtotal) as total FROM penjualan_detail as x GROUP BY x.id_penjualan_header) as c"), 'a.id', '=', 'c.id_penjualan_header')
                ->select('a.id', 'a.no_penjualan', 'a.status_pembayaran', DB::raw('IFNULL(c.total, 0) as total'))
                ->where('a.id', $id)
                ->where('a.user_id_created', Auth::user()->id)
                ->first();

            $debug['transaction_found'] = $penjualan ? 'YES' : 'NO';
            if ($penjualan) {
                $debug['transaction_data'] = $penjualan;
            }

            return response()->json($debug);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
