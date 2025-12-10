$(document).ready(function () {
	ProductsOrders.load();
});

var ProductsOrders = (function () {

	var addProduct = function () {
		$("#add-product").on("click", function () {
			event.preventDefault();
			if ($("#select-product").val() && $("#input-quantity").val()) {
				if ($("#input-quantity").val() >= 1) {
					var data_ = {};
					var product_name = $("#select-product option:selected").text();
					data_.product_id = $('#select-product').val();
					data_.quantity = $('#input-quantity').val();
					data_.order_type_id = $('#order_type_id').val();
					$("#select-product").val("").trigger("change");
					$("#input-quantity").val("");
					if ($(".item-product[data-product_id='" + data_.product_id + "']").length > 0) {
						swal({
							title: $.i18n._("Order.Product_already_added"),
							type: "error",
						}).then(function () { });
						return;
					}
					var table_row_1 =
						"<tr>" +
						"<td class='item-product' " +
						"data-product_id='" + data_.product_id + "'>" + product_name +
						"</td>" +
						"<td class='quantity ta-center'>" + data_.quantity + "</td>";
					var table_row_2 =
						"<td class='ta-center btn-hide'>" +
						"<span class='ion-android-cancel cursor-pointer c-fallo delete-garage-product'></span>" +
						"</td>" +
						"</tr>"
					$('#product-tbody').append(table_row_1 + table_row_2);
					var newOption = new Option(product_name, data_.product_id + ' ' + data_.quantity + ' ' + data_.order_type_id, true, true);
					$('#garages-products').append(newOption).trigger('change');
					updateContent();
				} else {
					swal({
						title: $.i18n._("Order.Mandatory_to_positive_quantity"),
						type: "error",
					}).then(function () { });
				}
			} else {
				swal({
					title: $.i18n._("Order.Product_quantity_mandatory"),
					type: "error",
				}).then(function () { });
			}
		});
	};

	var updateContent = function () {
		$('.table-tracking').basictable('destroy');
		$('.table-tracking').basictable();
		$("#product-tbody li").removeClass('ui-state-default');
		$("#product-tbody li span").remove();
		deleteGarageProduct();
	};

	var deleteGarageProduct = function () {
		$('.delete-garage-product').off('click').on('click', function () {
			var parent_tr = $(this).closest('tr');
			swal({
				title: $.i18n._('Products.Confirm_delete'),
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._('General.Yes'),
				cancelButtonText: $.i18n._('General.No'),
			}).then(function (result) {
				if (result.value) {
					var data = {};
					data.garage_product_id = parent_tr.find('.item-product').data('id');
					data.product_id = parent_tr.find('.item-product').data('product_id');
					data.order_type_id = $('#order_type_id').val();
					if (data.garage_product_id === undefined) {
						// no inserted in db yet, just remove from the "to be inserted" select
						$('#garages-products > option').each(function () {
							if (this.value.startsWith(data.product_id)) {
								$(this).remove();
								$('#garages-products').trigger('change');
							}
						});
					} else {
						// already inserted in bd, add to the "to be deleted" select
						var newOption = new Option(data.order_type_id, data.garage_product_id, true, true);
						$('#garages-products-delete').append(newOption).trigger('change');
					}
					parent_tr.remove();
					updateContent();
				}
			});
		});
	};

	var loadGarageProductListOrderType = function () {
		$('#order_type_id').val($('#order_type').val());
		$('#select-product').empty().trigger('change');
		$('#select-product').append('<option value=""></option>');

		var url = $('#order_type').data('url');
		var data = {};
		data['id'] = $('#order_type').val();

		var request = PeticionAjax.post(url, data);

		request.done(function (result) {
			resultado = JSON.parse(result);
			$.each(resultado, function (index, value) {
				$('#select-product').append($('<option>').text(value).attr('value', index)).trigger('change');
			});
		});
		$("#select-product").val("").trigger("change");
	}

	var deleteProductsTableWhenChangeOrderType = function () {
		//Empty the selector of products that were going to be added but are no longer there
		$('#garages-products').empty().trigger('change');
		//The list of options to be deleted is loaded with those garage products that are in db.
		$('#product-tbody').find('tr').each(function () {
			var garage_product_id = $(this).closest('tr').find('.item-product').data('id');
			var garage_id = $('#garage_id').val();
			if (garage_product_id !== undefined) {
				var newOption = new Option(garage_id, garage_product_id, true, true);
				$('#garages-products-delete').append(newOption).trigger('change');
			}
		});
		$("#product-tbody tr").remove();
	}

	var checkOrderTypeChange = function () {
		$('#order_type').change(function () {
			if (($('#order_type_id').val() !== $('#order_type').val())) {	//If there is a change in the order type (current !== new one)
				if ($('#order_type_id').val() !== '' && $('#product-tbody').find('tr').length > 0) {	// If action is edit
					swal({
						title: $.i18n._("Orden.Confirm_change_order_type"),
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: primary_color,
						confirmButtonText: $.i18n._('General.Yes'),
						cancelButtonText: $.i18n._('General.No'),
					}).then(function (result) {
						if (result.value) {
							loadGarageProductListOrderType();
							deleteProductsTableWhenChangeOrderType();
						} else {
							$('#order_type').val($('#order_type_id').val()).trigger("change");
						}
					});
				} else {	//First time you enter into add form
					loadGarageProductListOrderType();
				}
			}
		});
	};

	var checkProducts = function () {
		$("#btn-guardar").on("click", function () {
			event.preventDefault();
			if ($('#product-tbody').find('tr').length > 0) {
				$("#order-form-id").submit();
			} else {
				swal({
					title: $.i18n._("Constants.Error_products"),
					type: "error",
				});
			}
		});
	};

	return {
		load: function () {
			loadGarageProductListOrderType();
			addProduct();
			deleteGarageProduct();
			checkOrderTypeChange();
			checkProducts();
		},
	};
})();
