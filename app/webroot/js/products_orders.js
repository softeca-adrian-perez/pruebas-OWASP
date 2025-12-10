$(document).ready(function () {
	ProductsOrders.load();
});

var ProductsOrders = (function () {
	var chargeProducts = function () {
		$(".tr-show").on("click", function () {
			var id = $(this).data("identificator");
			if ($("#" + $(this).attr("id") + " div.ampliar-js").hasClass("abierto")) {
				chargeData(id);
			}
			$("#table-orders > tbody > tr").each(function () {
				if ($(this).hasClass("orders-" + id)) {
					$(this).fadeToggle();
				}
			});
		});
	};

	var chargeData = function (id) {
		$("tr.orders-" + id).each(function () {
			$(this).remove();
		});
		var url = $("#" + id).data("url");
		var request = PeticionAjax.post(url);
		request.done(function (result) {
			result = JSON.parse(result);
			let texto = "";
			$.each(result, function (index, value) {
				texto +=
					'<tr class="orders-' +
					id +
					'"> <td colspan="3"> </td> <td colspan="1"> ' +
					value["name"] +
					' </td> <td colspan="7"> ' +
					value["quantity"] +
					" </td> <td class='ta-center btn-hide' " +
					($("#edit-btn-disable").val() == 1 ? "hidden" : "") +
					" > " + " </td> </tr>";
			});
			$("#" + id).after(texto);
		});
	};

	return {
		load: function () {
			chargeProducts();
		},
	};
})();