
function showErrorMessage(id, message) {
  $("#" + id + "").addClass("is-invalid");
  let closestDiv = $("#" + id + "")
    .closest("div")
    .find(".text-danger");
  closestDiv.text(message);
  closestDiv.removeClass("d-none");
}
function validateSelect2Field($select, fieldName) {
  let value = $select.val();
  let $select2Container = $select.next(".select2"); 
  if (!value) {
    $select2Container.find(".select2-selection").addClass("is-invalid");
    if (!$select2Container.next(".text-danger").length) {
      $select2Container.after(
        '<div class="text-danger">The ' + fieldName + " field is required</div>"
      );
    }
    return false;
  } else {
    $select2Container.find(".select2-selection").removeClass("is-invalid");
    $select2Container.next(".text-danger").remove();
    return true;
  }
}
/* For Customer IO */
let i = 0;
$(document).on("click", "#customer_io", function (e) {
  ++i;
  $(".errProduct").remove();
  let hidden_type = $("#hidden_type").html();
  let firstInterState = $('input[name^="inter_state["]:checked').first().val() || "N";
  $(".add_cio").append(
    "<tr>" +
      '<td class="ir_txt_center"><p class="set_sn rowCount">' +
      i +
      "</p></td>" +
      '<td><select class="form-control type select2" name="type[]" id="type_' +
      i +
      '" style="min-width: 120px;"><option value="">Please Select</option>\n' +
      hidden_type +
      "</select></td>" +
      '<td><select class="form-control ins_category select2" name="ins_category[]" id="ins_category_' +
      i +
      '" style="min-width: 120px;"><option value="">Please Select</option>\n' +
      "</select></td>" +
      '<td><select class="form-control ins_name select2" name="ins_name[]" id="ins_name_' +
      i +
      '" style="min-width: 120px;"><option value="">Please Select</option>\n' +
      "</select></td>" +
      '<td><input type="number" name="qty[]" class="check_required form-control integerchk qty_c" placeholder="Quantity" id="quantity_' +
      i +
      '" style="min-width: 120px;"></td>' +
      '<td><input type="number" name="rate[]" class="check_required form-control integerchk rate_c" placeholder="Rate" id="rate_' +
      i +
      '" value="0.00" style="min-width: 120px;"></td>' +
      '<td><input type="number" name="total[]" class="check_required form-control integerchk total_c" placeholder="Total" id="total_' +
      i +
      '" readonly style="min-width: 120px;" value="0.00"></td>' +
      '<td><input type="number" name="tax_rate[]" class="check_required form-control integerchk tax_percent_c" placeholder="Tax Rate" id="tax_rate_' +
      i +
      '" style="min-width: 120px;"></td>' +
      '<td><div class="form-group radio_button_problem"><div class="radio">' +
      '<label><input type="radio" name="inter_state[' +
      i +
      ']" id="inter_state_yes_' +
      i +
      '" value="Y" ' +
      (i === 1
        ? "checked"
        : firstInterState === "Y"
        ? "checked disabled"
        : "disabled") +
      "> Yes</label>&nbsp;" +
      '<label><input type="radio" name="inter_state[' +
      i +
      ']" id="inter_state_no_' +
      i +
      '" value="N" ' +
      (i === 1
        ? "checked"
        : firstInterState === "N"
        ? "checked disabled"
        : "disabled") +
      "> No</label>" +
      (i !== 1
        ? '<input type="hidden" name="inter_state[' +
          i +
          ']" value="' +
          firstInterState +
          '">'
        : "") +
      "</div></div></td>" +
      '<td class="cgst_cell" style="display:none;"><input type="text" name="cgst[]" class="form-control cgst_input" id="cgst_' +
      i +
      '" readonly></td>' +
      '<td class="sgst_cell" style="display:none;"><input type="text" name="sgst[]" class="form-control sgst_input" id="sgst_' +
      i +
      '" readonly></td>' +
      '<td class="igst_cell" style="display:none;"><input type="text" name="igst[]" class="form-control igst_input" id="igst_' +
      i +
      '" readonly></td>' +      
      '<td><input type="number" name="tax_amount[]" class="check_required form-control integerchk tax_amount_c" placeholder="Tax Amount" id="tax_amount_' +
      i +
      '" readonly style="min-width: 120px;" value="0.00"></td>' +
      '<td><input type="number" name="subtotal[]" class="check_required form-control integerchk subtotal_c" placeholder="Subtotal" id="subtotal_' +
      i +
      '" readonly style="min-width: 120px;" value="0.00"></td>' +
      '<td><textarea class="form-control" name="remarks[]" placeholder="Remarks" id="remarks" style="min-width: 150px;"></textarea></td>' +
      '<td class="ir_txt_center"><a class="btn btn-xs del_row remove-tr dlt_button"><iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon></a></td>' +
      "</tr>"
    );
    let newRow = $(".add_cio").find("tr").last();
    let tax_rate = 0;
    let interStateVal =
      newRow.find('input[type=radio][name^="inter_state"]:checked').val() ||
      "N";
    let isYes = interStateVal === "Y";
    applyGSTLogic(newRow, isYes, tax_rate);
    $(".select2").select2();
});
function update_invoice_totals() {
  let total_sub_total = 0;
  $(".subtotal_c").each(function () {
    total_sub_total += parseFloat($(this).val()) || 0;
  });
  // console.log("total_sub_total", total_sub_total);

  $("#total_sub_total").val(total_sub_total.toFixed(2));
}
function updateGSTHeaders(showIGST) {
  if (showIGST) {
    $("#igst_th").hide();
    $("#cgst_th, #sgst_th").show();
  } else {
    $("#cgst_th, #sgst_th").hide();
    $("#igst_th").show();
  }
}
function applyGSTLogic(row, isYes, tax_rate) {
  if (!row) {
    $("table tbody tr").each(function () {
      applyGSTLogic($(this), isYes, tax_rate);
    });
    return;
  }
  if (isYes) {
    // console.log("Applying IGST logic");
    row.find(".cgst_cell, .sgst_cell").hide();
    row.find(".igst_cell").show();
    row.find(".igst_input").val(tax_rate);
    row.find(".cgst_input, .sgst_input").val("");
  } else {
    // console.log("Applying CGST logic");
    row.find(".cgst_cell, .sgst_cell").show();
    row.find(".igst_cell").hide();

    let half_rate = Math.round(tax_rate / 2);
    row.find(".cgst_input").val(half_rate);
    row.find(".sgst_input").val(half_rate);
    row.find(".igst_input").val("");
  }

  updateGSTHeaders(!isYes);
  cal_row(row);
  update_invoice_totals();
}
$(document).on("change", 'input[name^="inter_state["]', function () {
  let newVal = $(this).val(); // 'Y' or 'N'
  let isYes = newVal === "Y";
  $('input[name^="inter_state["]').each(function () {
    let name = $(this).attr("name");
    let index = name.match(/\d+/)[0];
    $('input[name="inter_state[' + index + ']"][value="' + newVal + '"]').prop("checked", true);
    let row = $(this).closest("tr");
    let tax_rate = parseFloat(row.find(".tax_percent_c").val()) || 0;
    applyGSTLogic(row, isYes, tax_rate);
  });
});
/* $(document).on("change", 'input[name="inter_state[1]"]', function () {
  let newVal = $(this).val();
  $('input[name^="inter_state["]').each(function () {
    let name = $(this).attr("name");
    if (name !== "inter_state[1]") {
      let index = name.match(/\d+/)[0];
      $(
        'input[name="inter_state[' + index + ']"][value="' + newVal + '"]'
      ).prop("checked", true);
      let row = $(this).closest("tr");
      let tax_rate = 0;
      let interStateVal =
        row
          .find('input[type=radio][name^="inter_state"]:checked')
          .val() || "N";
      let isYes = interStateVal === "Y";
      applyGSTLogic(row, isYes, tax_rate);
    }
  });
}); */
$(document).on("click", ".order_io_submit_button", function () {
  let status = true;
  let po_no = $("#po_no").val();
  let date = $("#io_date").val();
  let c_phn_no = $("#c_phn_no").val();
  let customer_name = $("#customer_name").val();
  let d_address = $("#d_address").val();
  let del_challan_no = $("#del_challan_no").val();
  let return_due_date = $("#return_due_date").val();
  // let type = $(".type").val();
  // let ins_category = $(".ins_category").val();
  // let instrument_name = $(".instrument_name").val();

  if (po_no == "" || po_no === null) {
    $("#po_no").addClass("is-invalid");
    $("#po_no").closest(".form-group").find(".text-danger").remove();
    $("#po_no").closest(".form-group")
        .append('<div class="text-danger">The po number field is required</div>');
    status = false;
  } else {
      $("#po_no").removeClass("is-invalid");
      $("#po_no").closest(".form-group").find(".text-danger").remove();
  }
  if (customer_name == "") {
    showErrorMessage("customer_name", "The customer name(code) field is required");
    status = false;
  } else {
    $("#customer_name").removeClass("is-invalid");
    $("#customer_name").closest("div").find(".text-danger").addClass("d-none");
  }
  if (del_challan_no == "") {
    showErrorMessage("del_challan_no", "The delivery challan number field is required");
    status = false;
  } else {
    $("#del_challan_no").removeClass("is-invalid");
    $("#del_challan_no").closest("div").find(".text-danger").addClass("d-none");
  }
  if (date == "") {
    showErrorMessage("io_date", "The delivery challan date field is required");
    status = false;
  } else {
    $("#io_date").removeClass("is-invalid");
    $("#io_date").closest("div").find(".text-danger").addClass("d-none");
  }
  if (return_due_date == "") {
    showErrorMessage("return_due_date", "The return date field is required");
    status = false;
  } else {
    $("#return_due_date").removeClass("is-invalid");
    $("#return_due_date").closest("div").find(".text-danger").addClass("d-none");
  }   
  if (c_phn_no == "") {
    showErrorMessage("c_phn_no", "The phone number field is required");
    status = false;
  } else {
    $("#c_phn_no").removeClass("is-invalid");
    $("#c_phn_no").closest("div").find(".text-danger").addClass("d-none");
  } 
  if ($("#d_address").val() == "") {
    $("#d_address").addClass("is-invalid");
    if (!$("#d_address").next(".text-danger").length) {
      $("#d_address").after(
        '<div class="text-danger">The delivery address field is required</div>'
      );
    }
    status = false;
  } else {
    $("#d_address").removeClass("is-invalid");
    $("#d_address").next(".text-danger").remove();
  }
  $("input[name='qty[]']").each(function () {
    let qty = $(this).val();
    if (qty === "") {
      $(this).addClass("is-invalid");
      if (!$(this).next(".text-danger").length) {
        $(this).after(
          '<div class="text-danger">The quantity is required</div>'
        );
      }
      status = false;
    } else {
      $(this).removeClass("is-invalid");
      $(this).next(".text-danger").remove();
    }
  });
  $("input[name='rate[]']").each(function () {
    let val = $(this).val().trim(); // get value as string
    let rate = parseFloat(val); // convert to number

    if (val === "" || isNaN(rate) || rate === 0) {
      $(this).addClass("is-invalid");
      if (!$(this).next(".text-danger").length) {
        $(this).after(
          '<div class="text-danger">The rate is required</div>'
        );
      }
      status = false;
    } else {
      $(this).removeClass("is-invalid");
      $(this).next(".text-danger").remove();
    }
  });
  $("input[name='tax_rate[]']").each(function () {
    let tax_rate = $(this).val();
    if (tax_rate === "") {
      $(this).addClass("is-invalid");
      if (!$(this).next(".text-danger").length) {
        $(this).after(
          '<div class="text-danger">The tax rate is required</div>'
        );
      }
      status = false;
    } else {
      $(this).removeClass("is-invalid");
      $(this).next(".text-danger").remove();
    }
  });

  let hasError = false;

  $("select[name='type[]']").each(function () {
    if (!validateSelect2Field($(this), "type")) {
      hasError = true;
    }
  });

  $("select[name='ins_category[]']").each(function () {
    if (!validateSelect2Field($(this), "category")) {
      hasError = true;
    }
  });

  $("select[name='ins_name[]']").each(function () {
    if (!validateSelect2Field($(this), "instrument name")) {
      hasError = true;
    }
  });


  if (hasError) {
    status = false;
  }

  let rowCount = $(".rowCount").length;
  if (!Number(rowCount)) {
    status = false;
    $("#ciofrm .add_cio").html(
      '<tr><td colspan="6" class="text-danger errProduct">Please add minimum one row</td></tr>'
    );
  } 

  if (status == true) {
    return true;
  } else {
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return false;
  }
});

/* For Partner IO */
function cal_row(row) {
  let unit_price = parseFloat(row.find(".rate_c").val()) || 0;
  let quantity = parseFloat(row.find(".qty_c").val()) || 0;
  let total_sub_total = 0;
  let sub_tot_bf = unit_price * quantity;
  // console.log("sub_tot_bf", sub_tot_bf);
  row.find(".total_c").val(sub_tot_bf.toFixed(2));

  let cgst = parseFloat(row.find(".cgst_input").val()) || 0;
  let sgst = parseFloat(row.find(".sgst_input").val()) || 0;
  let igst = parseFloat(row.find(".igst_input").val()) || 0;
  let gst_percent = cgst + sgst + igst;
  let gst_amount = sub_tot_bf * (gst_percent / 100);
  let subtotal_with_gst = sub_tot_bf + gst_amount;
  // let gst_percent = parseFloat(row.find(".tax_percent_c").val()) || 0;
  // let gst_amount = sub_tot_bf * (gst_percent / 100);
  // let subtotal_with_gst = sub_tot_bf + gst_amount;
  row.find(".tax_amount_c").val(gst_amount.toFixed(2));
  row.find(".subtotal_c").val(subtotal_with_gst.toFixed(2));
  $(".subtotal_c").each(function () {
    total_sub_total += parseFloat($(this).val()) || 0;
  });
  // console.log("total_sub_total", total_sub_total);
  $("#total_amount").val(total_sub_total.toFixed(2));
}
$(document).on("keyup", ".rate_c", function (e) {
  let row = $(this).closest("tr");
  cal_row(row);
});
$(document).on("input", ".qty_c", function (e) {
  let row = $(this).closest("tr");
  cal_row(row);
});
$(document).on("keyup", ".tax_percent_c", function (e) {
  let row = $(this).closest("tr");
  cal_row(row);
});
$(document).on("keyup", ".subtotal_c", function (e) {
  let row = $(this).closest("tr");
  cal_row(row);
});
$(document).on("input change", ".qty_c, .rate_c, .tax_percent_c", function () {
  let row = $(this).closest("tr");
  let qty = parseFloat(row.find(".qty_c").val()) || 0;
  let rate = parseFloat(row.find(".rate_c").val()) || 0;
  let tax_rate = parseFloat(row.find(".tax_percent_c").val()) || 0;

  // Determine if it's interstate
  let interStateVal = row.find('input[type=radio][name^="inter_state"]:checked').val() || "N";
  let isYes = interStateVal === "Y";

  // Apply GST logic + recalc
  applyGSTLogic(row, isYes, tax_rate);
});
let j = 0;

$(document).on("click", "#partner_io", function (e) {
  ++j;
  let hidden_type = $("#hidden_type").html();
  let hidden_unit = $("#hidden_unit").html();
  let partnerFirstInterState = $('input[name^="inter_state["]:checked').first().val() || "N";
  $(".add_partner").append(
    "<tr>" +
      '<td class="ir_txt_center"><p class="set_sn rowCount">' +
      j +
      "</p></td>" +
      '<td><select class="form-control type select2" name="type[]" id="type_' +
      j +
      '" style="min-width: 150px;"><option value="">Please Select</option>\n' +
      hidden_type +
      "</select></td>" +
      '<td><select class="form-control ins_category select2" name="ins_category[]" id="ins_category_' +
      j +
      '" style="min-width: 150px;"><option value="">Please Select</option>\n' +
      "</select></td>" +
      '<td><select class="form-control ins_name select2" name="ins_name[]" id="ins_name_' +
      j +
      '" style="min-width: 150px;"><option value="">Please Select</option>\n' +
      "</select></td>" +
      '<td><input type="number" name="qty[]" class="check_required form-control integerchk qty_c" placeholder="Quantity" id="quantity_' +
      j +
      '" style="min-width: 120px;"></td>' +
      '<td><input type="number" name="rate[]" class="check_required form-control integerchk rate_c" placeholder="Rate" id="rate_' +
      j +
      '" value="0.00" style="min-width: 120px;"></td>' +
      '<td><input type="number" name="total[]" class="check_required form-control integerchk total_c" placeholder="Total" id="total_' +
      j +
      '" readonly style="min-width: 120px;" value="0.00"></td>' +      
      '<td><input type="number" name="tax_rate[]" class="check_required form-control integerchk tax_percent_c" placeholder="Tax Rate" id="tax_rate_' +
      j +
      '" style="min-width: 120px;"></td>' +
      '<td><div class="form-group radio_button_problem"><div class="radio">' +
      '<label><input type="radio" name="inter_state[' +
      j +
      ']" id="inter_state_yes_' +
      j +
      '" value="Y" ' +
      (j === 1
        ? "checked"
        : partnerFirstInterState === "Y"
        ? "checked disabled"
        : "disabled") +
      "> Yes</label>&nbsp;" +
      '<label><input type="radio" name="inter_state[' +
      j +
      ']" id="inter_state_no_' +
      j +
      '" value="N" ' +
      (j === 1
        ? "checked"
        : partnerFirstInterState === "N"
        ? "checked disabled"
        : "disabled") +
      "> No</label>" +
      (j !== 1
        ? '<input type="hidden" name="inter_state[' +
          j +
          ']" value="' +
          partnerFirstInterState +
          '">'
        : "") +
      "</div></div></td>" +
      '<td class="cgst_cell" style="display:none;"><input type="text" name="cgst[]" class="form-control cgst_input" id="cgst_' +
      j +
      '" readonly></td>' +
      '<td class="sgst_cell" style="display:none;"><input type="text" name="sgst[]" class="form-control sgst_input" id="sgst_' +
      j +
      '" readonly></td>' +
      '<td class="igst_cell" style="display:none;"><input type="text" name="igst[]" class="form-control igst_input" id="igst_' +
      j +
      '" readonly></td>' + 
      '<td><input type="number" name="tax_amount[]" class="check_required form-control integerchk tax_amount_c" placeholder="Tax Amount" id="tax_amount_' +
      j +
      '" readonly style="min-width: 120px;" value="0.00"></td>' +
      '<td><input type="number" name="subtotal[]" class="check_required form-control integerchk subtotal_c" placeholder="Subtotal" id="subtotal_' +
      j +
      '" readonly style="min-width: 120px;" value="0.00"></td>' +
      '<td><textarea class="form-control" name="remarks[]" placeholder="Remarks" id="remarks_' +
      j +
      '" style="min-width: 130px;"></textarea></td>' +
      '<td><input type="text" name="line_item_no[]" class="form-control" placeholder="Line Item No" style="min-width: 130px;"/></td>' +
      '<td class="ir_txt_center"><a class="btn btn-xs del_row remove-tr dlt_button"><iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon></a></td>' +
      "</tr>"
  );
  let newRow = $(".add_partner").find("tr").last();
  let tax_rate = 0;
  let interStateVal =
  newRow.find('input[type=radio][name^="inter_state"]:checked').val() || "N";
  let isYes = interStateVal === "Y";
  applyGSTLogic(newRow, isYes, tax_rate);
  $(".select2").select2();
});

$(document).on("click",".partner_io_submit_button", function() {
  let status = true;
  let reference_no = $("#reference_no").val();
  let partner_id = $("#partner_id").val();
  let io_date = $("#io_date").val();
  let phn_no = $("#phn_no").val();
  let del_challan_no = $("#del_challan_no").val();
  let return_due_date = $("#return_due_date").val();
  let d_address = $("#d_address").val();

  if (reference_no == "") {
    showErrorMessage("reference_no", "The reference number field is required");
    status = false;
  } else {
    $("#reference_no").removeClass("is-invalid");
    $("#reference_no").closest("div").find(".text-danger").addClass("d-none");
  }
  if (partner_id == "") {
    showErrorMessage("partner_id", "The partners(code) field is required");
    status = false;
  } else {
    $("#partner_id").removeClass("is-invalid");
    $("#partner_id").closest("div").find(".text-danger").addClass("d-none");
  } 
  if (del_challan_no == "") {
    showErrorMessage("del_challan_no", "The delivery challan number field is required");
    status = false;
  } else {
    $("#del_challan_no").removeClass("is-invalid");
    $("#del_challan_no").closest("div").find(".text-danger").addClass("d-none");
  }
  if (io_date == "") {
    showErrorMessage("io_date", "The delivery challan date field is required");
    status = false;
  } else {
    $("#io_date").removeClass("is-invalid");
    $("#io_date").closest("div").find(".text-danger").addClass("d-none");
  } 
  if (return_due_date == "") {
    showErrorMessage("return_due_date", "The return date field is required");
    status = false;
  } else {
    $("#return_due_date").removeClass("is-invalid");
    $("#return_due_date").closest("div").find(".text-danger").addClass("d-none");
  }
  if (phn_no == "") {
    showErrorMessage("phn_no", "The phone number field is required");
    status = false;
  } else {
    $("#phn_no").removeClass("is-invalid");
    $("#phn_no").closest("div").find(".text-danger").addClass("d-none");
  } 
  if ($("#d_address").val() == "") {
    $("#d_address").addClass("is-invalid");
    $("#d_address").closest(".form-group").find(".text-danger").removeClass("d-none").text("The delivery address field is required");
    status = false;
  } else {
    $("#d_address").removeClass("is-invalid");
    $("#d_address").closest(".form-group").find(".text-danger").addClass("d-none").text("");
  }
  $("input[name='qty[]']").each(function () {
    let qty = $(this).val();
    if (qty === "") {
      $(this).addClass("is-invalid");
      if (!$(this).next(".text-danger").length) {
        $(this).after(
          '<div class="text-danger">The quantity is required</div>'
        );
      }
      status = false;
    } else {
      $(this).removeClass("is-invalid");
      $(this).next(".text-danger").remove();
    }
  });
  $("input[name='rate[]']").each(function () {
    let val = $(this).val().trim(); // get value as string
    let rate = parseFloat(val); // convert to number

    if (val === "" || isNaN(rate) || rate === 0) {
      $(this).addClass("is-invalid");
      if (!$(this).next(".text-danger").length) {
        $(this).after(
          '<div class="text-danger">The rate is required</div>'
        );
      }
      status = false;
    } else {
      $(this).removeClass("is-invalid");
      $(this).next(".text-danger").remove();
    }
  });
  $("input[name='tax_rate[]']").each(function () {
    let tax_rate = $(this).val();
    if (tax_rate === "") {
      $(this).addClass("is-invalid");
      if (!$(this).next(".text-danger").length) {
        $(this).after(
          '<div class="text-danger">The tax rate is required</div>'
        );
      }
      status = false;
    } else {
      $(this).removeClass("is-invalid");
      $(this).next(".text-danger").remove();
    }
  });

  let hasError = false;

  $("select[name='type[]']").each(function () {
    if (!validateSelect2Field($(this), "type")) {
      hasError = true;
    }
  });

  $("select[name='ins_category[]']").each(function () {
    if (!validateSelect2Field($(this), "category")) {
      hasError = true;
    }
  });

  $("select[name='ins_name[]']").each(function () {
    if (!validateSelect2Field($(this), "instrument name")) {
      hasError = true;
    }
  });

  $("input[name='line_item_no[]']").each(function () {
    let value = $(this).val();
    if (!value || value.trim() === "") {
      if (!$(this).next(".text-danger").length) {
        $(this).addClass("is-invalid");
        $(this).after(
          '<div class="text-danger">The line item number field is required</div>'
        );
      }
      hasError = true;
    } else {
      $(this).removeClass("is-invalid");
      $(this).next(".text-danger").remove();
    }
  });

  if (hasError) {
    status = false;
  }
  let rowCount = $(".rowCount").length;
  if (!Number(rowCount)) {
    status = false;
    $("#piofrm .add_partner").html(
      '<tr><td colspan="6" class="text-danger errProduct">Please add minimum one row</td></tr>'
    );
  }

  if (status == true) {
    return true;
  } else {
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return false;
  } 
});
$(document).on("change", ".type", function () {
  let current = $(this);
  let type = current.find(":selected").val();
  let hidden_base_url = $("#hidden_base_url").val();
  let row = current.closest("tr");
  $.ajax({
    type: "POST",
    url: hidden_base_url + "getInstrumentCategory",
    data: { id: type },
    success: function (data) {
      let select = row.find(".ins_category");
      select.html(data);
    },
    error: function () {},
  });
});
$(document).on("change", ".ins_category", function () {
  let current = $(this);
  let row = current.closest("tr");
  let type = row.find(".type").val();
  let ins_category = row.find(".ins_category").val();
  let hidden_base_url = $("#hidden_base_url").val();
  $.ajax({
    type: "POST",
    url: hidden_base_url + "getInstruments",
    data: {
      type: type,
      ins_category: ins_category,
    },
    success: function (data) {
      let select = row.find(".ins_name");
      select.html(data);
    },
    error: function () {},
  });
});

$(document).on("change", ".ins_name", function () {
  let hidden_alert = $("#hidden_alert").val();
  let hidden_cancel = $("#hidden_cancel").val();
  let hidden_ok = $("#hidden_ok").val();

  let current = $(this);
  let row = current.closest("tr");

  let selected_type = row.find(".type").val();
  let selected_category = row.find(".ins_category").val();
  let selected_instrument = row.find(".ins_name").val();

  let isDuplicate = false;

  $(".add_cio tr").each(function () {
    if (this !== row[0]) {
      let other_type = $(this).find(".type").val();
      let other_category = $(this).find(".ins_category").val();
      let other_instrument = $(this).find(".ins_name").val();

      if (
        selected_type &&
        selected_category &&
        selected_instrument &&
        selected_type === other_type &&
        selected_category === other_category &&
        selected_instrument === other_instrument
      ) {
        swal({
          title: hidden_alert + "!",
          text: "This Instrument already exists.",
          cancelButtonText: hidden_cancel,
          confirmButtonText: hidden_ok,
          confirmButtonColor: "#3c8dbc",
        });
        current.val("").trigger("change");
        isDuplicate = true;
        return false;
      }
    }
  });

  if (isDuplicate) return;
});


$(document).on("click", ".del_row", function (e) {
  $(this).parent().parent().remove();
});

/* IO date */
$(document).ready(function () {
  $("#io_date")
  .datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
    // startDate: new Date(),
  });
  $("#return_due_date")
  .datepicker({
    format: "dd-mm-yyyy",
    autoclose: true,
    todayHighlight: true,
    // startDate: new Date(),
  });
  // .datepicker("update", new Date());  
});

  $(document).on("click", ".open-calendar", function () {
    let id = $(this).data("id");
    // console.log("Selected ID:", id);
    $("#calendarModal").find('input[name="customer_io_id"]').val(id);
  });


/* To Get Customer name from po number */
$(document).on("change", "#po_no", function (e) {
 let lineItemNo = $(this).find(":selected").data("lineitem");
 $("#line_item_no").val(lineItemNo);
  let po_no = $(this).find(":selected").val();
  let hidden_base_url = $("#hidden_base_url").val();

  $.ajax({
    type: "POST",
    url: hidden_base_url + "getCustomerName",
    data: { po_no: po_no },
    dataType: "json",
    success: function (data) {
      if (data) {
        $('#customer_id').val(data.id)
        $("#customer_name").val(data.name + " (" + data.customer_id + ")");
        $("#c_phn_no").val(data.phone);
        $("#c_email").val(data.email);
        $("#d_address").val(data.address);
      }
    },
    error: function () {},
  });
});
/* To Get partner  */
$(document).on("change", "#partner_id", function (e) {
   let partner_id = $(this).val(); 
  let hidden_base_url = $("#hidden_base_url").val();

  $.ajax({
    type: "POST",
    url: hidden_base_url + "getPartner",
    data: { partner_id: partner_id },
    dataType: "json",
    success: function (data) {
      if (data) {
        $("#phn_no").val(data.phone);
        $("#email").val(data.email);
        $("#d_address").val(data.address);
      }
    },
    error: function () {},
  });
});

