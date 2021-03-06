<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {
 
    function __construct()
    {
		parent::__construct();
		$this->load->library('grocery_CRUD');
		$this->load->library('OutputView');
		$this->load->model('crud_model');
        $this->load->model('Notification_model');
        $this->load->model('User_groups_model');
		$this->load->model('Spkinduk_model');
    }
	
	public function mesinreport ()
	{
		$crud = new Grocery_CRUD();
		$crud->set_subject('Report Per Mesin');
        $crud->set_table('view_reportmesin');
		$crud->set_primary_key('id');
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();
		$crud->unset_read();
		$crud->display_as('jumlahspk','Jumlah SPK');
		$crud->unset_print(); 
		$crud->unset_columns('id');
		$output = $crud->render();
        
        $data['judul'] = 'Report Mesin';
        $data['crumb'] = array('Report'=>' ', 'Mesin'=>'');
        $view = 'grocery'; $template='metronic_template';
        $this->outputview->output_admin($view, $template, $data, $output);
	}

	public function po_receive($id)
	{
		//$output = (object)array('data' => '' , 'output' => '' , 'js_files' => null , 'css_files' => null);

        $data['judul'] = 'PURCHASE ORDER RECEIVED';
        
        //$value = $this->crud_model->select('po','*','id='.$id)->result();
        $value = $this->crud_model->relation('po','gudang','customer',null,'po.gudang_id=gudang.gudang_id','po.customer_id=customer.id',null,'*','po.id='.$id, null)->result();
        $senddate = new DateTime($value[0]->tanggal_kirim);
        $product_id= $this->crud_model->select('po','*', 'id='.$id, null, null, null)->result();
    
        $output['param'] = 'nprod';
        $output['valuetable'] = $this->crud_model->relation('po','product',null,null,'product.product_id in('.$product_id[0]->product_id.')',null,null,'*','po.id='.$id, null)->result();
        $output['po_product'] = $this->crud_model->select('po_product','*','po_id='.$id,null,null,null,null)->result();
        $output['no'] = $value[0]->no_po;
        $output['tgl'] = $this->tanggal_indo($value[0]->tanggal_po);
        $output['currency'] = $value[0]->mata_uang;
        $output['send_date'] = $senddate->format('d/m/Y');
        $output['gudang'] = $value[0]->gudang_name;
        $output['customername'] = $value[0]->customer_name;
        $output['customercontact'] = $value[0]->customer_contact;
        $output['customeraddress'] = $value[0]->customer_address;

        $template = 'metronic_template';
        $view = 'po_receive';
        $this->outputview->output_admin($view, $template, $data, $output);
	}

	public function po_receive_prod($id)
	{
		//$output = (object)array('data' => '' , 'output' => '' , 'js_files' => null , 'css_files' => null);

        $data['judul'] = 'PURCHASE ORDER RECEIVED';

        $value = $this->crud_model->relation('po','gudang','customer',null,'po.gudang_id=gudang.gudang_id','po.customer_id=customer.id',null,'*','po.id='.$id, null)->result();
        $senddate = new DateTime($value[0]->tanggal_kirim);
        $product_id= $this->crud_model->select('po','*', 'id='.$id, null, null, null)->result();

        //output
        $output['param'] = 'prod';
        $output['valuetable'] = $this->crud_model->relation('po','product',null,null,'product.product_id in('.$product_id[0]->product_id.')',null,null,'*','po.id='.$id, null)->result();
        
        $output['no'] = $value[0]->no_po;
        $output['tgl'] = $this->tanggal_indo($value[0]->tanggal_po);
        $output['currency'] = $value[0]->mata_uang;
        $output['send_date'] = $senddate->format('d/m/Y');
        $output['po_product'] = $this->crud_model->select('po_product','*','po_id='.$id,null,null,null,null)->result();
        $output['gudang'] = $value[0]->gudang_name;
        $output['customername'] = $value[0]->customer_name;
        $output['customercontact'] = $value[0]->customer_contact;
        $output['customeraddress'] = $value[0]->customer_address;

        $template = 'metronic_template';
        $view = 'po_receive';
        $this->outputview->output_admin($view, $template, $data, $output);
	}

    public function cetakspk($spk_proses_id)
    {
        $value = $this->crud_model->relation('spk_induk','spk_proses_mesin', 'po', 'product', 'spk_induk.spk_induk_id=spk_proses_mesin.spk_induk','spk_induk.po_id=po.id','po.product_id=product.product_id','*','spk_proses_mesin.spk_proses_id='.$spk_proses_id, null)->result();

        $proses = $value[0]->proses_type;
        switch ($proses) {
            case 1:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'CETAK';
                break;
            case 2:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'VARNISH';
                break;
            case 9:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'SORTIR';
                break;
            case 5:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'POTONG';
                break;
            case 6:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'GLUING';
                break;
            case 7:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'PLATE';
                break;
            case 8:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'POND';
                break;
            case 10:
                $data['judul'] = 'SPK PRODUKSI';
                $data['subjudul'] = 'PACKING';
                break;
        }

        //$data['judul'] = 'SPK PRODUKSI';
        //$data['subjudul'] = 'VARNISH';

        $output['data'] = $value[0];
        $output['param'] = $value[0]->proses_type;
        $output['param1'] = 'text-center';

        $template = 'metronic_template';
        $view = 'spk';
        $this->outputview->output_admin($view, $template, $data, $output);

    }

	public function report_spk_induk($spk_induk)
    {
        //$output = (object)array('data' => '' , 'output' => '' , 'js_files' => null , 'css_files' => null);

        $data['judul'] = 'PRODUKSI';
        $data['crumb'] = array( 'SPK' => 'purchase/spk_induk', 'Report' => '');

        $value = $this->crud_model->relation('spk_induk','gudang',null,null,'spk_induk.gudang_asal=gudang.gudang_id',null,null,'*','spk_induk.spk_induk_id='.$spk_induk, null)->result();
        $gudangtujuan = $this->crud_model->relation('spk_induk','gudang',null,null,'spk_induk.gudang_tujuan=gudang.gudang_id',null,null,'gudang_name','spk_induk.spk_induk_id='.$spk_induk, null)->result();
        $valuetable = $this->crud_model->relation('spk_induk','spk_material','material','gudang','spk_induk.spk_induk_id=spk_material.spk_induk_id','spk_material.kode_material=material.id','spk_material.gudang_id=gudang.gudang_id','material.material_name, spk_material.qty_deliver, spk_material.satuan, gudang.gudang_name, material.harga_satuan','spk_induk.spk_induk_id='.$spk_induk, null)->result();
        $valuehasil = $this->crud_model->relation('spk_induk', 'po','product', 'gudang','spk_induk.po_id=po.id', 'po.product_id=product.product_id', 'spk_induk.gudang_tujuan=gudang.gudang_id','product.product_kode, product.product_name, po.jumlah_unit,
            product.product_unit, product.product_price, gudang.gudang_name','spk_induk.spk_induk_id='.$spk_induk, null)->result();

        //output
        $output['hasilProduksi'] = $valuehasil[0];
        $output['spkdata'] = $valuetable;
        $output['nomor'] = $value[0]->spk_induk_id;
        $output['tgl'] = $this->tanggal_indo($value[0]->spk_date);
        $output['desc'] = $value[0]->description;
        $output['gdasal'] = $value[0]->gudang_name;
       	$output['gdtujuan'] = $gudangtujuan[0]->gudang_name;
        $output['param'] = 'nonprod';

        $template = 'metronic_template';
        $view = 'report';
        $this->outputview->output_admin($view, $template, $data, $output);
    }

    public function tanggal_indo($tanggal)
	{
		$bulan = array (1 =>   'Januari',
					'Februari',
					'Maret',
					'April',
					'Mei',
					'Juni',
					'Juli',
					'Agustus',
					'September',
					'Oktober',
					'November',
					'Desember'
				);


		$split = explode('-', $tanggal);
		return $bulan[ (int)$split[1] ] . ' ' . $split[2] . ', ' . $split[0];
	}

    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
        }     
        return $temp;
    }
	
	public function spkreport ()
	{
		$data['judul'] = 'SPK INDUK REPORT';
		$template = 'metronic_template';
        $view = 'spkindukreport';
        $this->outputview->output_admin($view, $template, $data);
	}
	
	public function spkmesinreport ()
	{
		$status = $this->input->post('status_spk');
		$data['valuespkinduk'] = $this->Spkinduk_model->getspk_by_status($status);
		$data['valuespkmesin'] = $this->Spkinduk_model->getspkmesin_by_status($status);
		
        
        $data['judul'] = 'REPORT SPK INDUK';
        $view = 'spkindukreport'; $template='metronic_template';
        $this->outputview->output_admin($view, $template, $data);
	}
}
