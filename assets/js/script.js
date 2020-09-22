
$(document).ready(function()
{

	//Confirm Delete Modal
	$('#confirm-delete').on('show.bs.modal', function(e) {
		var string = document.getElementById("confirm-delete").innerHTML;
		var hasil = string.replace("fa fa-text-width text-yellow","fa fa-exclamation-triangle text-red");
		document.getElementById("confirm-delete").innerHTML = hasil;

		var string2 = document.getElementById("confirm-delete").innerHTML;
		var hasil2 = string2.replace("Konfirmasi", "&nbspKonfirmasi");
		document.getElementById("confirm-delete").innerHTML = hasil2;
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
	});

	$('.tgl_mulai').datetimepicker({
		locale:'id',
		format: 'DD-MM-YYYY',
		useCurrent: false,
		date: moment(new Date())
	});
	$('.tgl_akhir').datetimepicker({
		locale:'id',
		format: 'DD-MM-YYYY',
		useCurrent: false,
		date: moment(new Date()).add(1, 'Y')
	});
	$('.tgl_mulai').datetimepicker().on('dp.change', function (e) {
		$('.tgl_akhir').data('DateTimePicker').minDate(moment(new Date(e.date)));
		$(this).data("DateTimePicker").hide();
		var tglAkhir = moment(new Date(e.date));
		tglAkhir.add(1, 'Y');
		$('.tgl_akhir').data('DateTimePicker').date(tglAkhir);
	});

	$('.select2-desa-ajax').select2({
	  ajax: {
	    url: function () {
	      return $(this).data('url');
	    },
	    dataType: 'json',
	    delay: 250,
	    data: function (params) {
	      return {
	        q: params.term || '', // search term
	        page: params.page || 1,
	      };
	    },
	    processResults: function (data, params) {
	      // parse the results into the format expected by Select2
	      // since we are using custom formatting functions we do not need to
	      // alter the remote JSON data, except to indicate that infinite
	      // scrolling can be used
	      // params.page = params.page || 1;

	      return {
	        results: data.results,
	        pagination: data.pagination
	      };
	    },
	    cache: true
	  },
		templateResult: function (desa) {
			if (! desa.id) {
			  return desa.text;
			}
			var $desa = $(
			  '<div>'+desa.text+'</div>'
			);
			return $desa;
		},
	  placeholder: '--  Cari Nama Desa --',
	  minimumInputLength: 0,
	});

});

function formAction(idForm, action, target = '')
{
	if (target != '')
	{
		$('#'+idForm).attr('target', target);
	}
	$('#'+idForm).attr('action', action);
	$('#'+idForm).submit();
}
