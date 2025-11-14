$(document).ready(function () {
    "use strict";
    /* $(document).on("change", "#purchase_mat_type", function (e) {
        let hidden_base_url = $("#hidden_base_url").val();
        let purchase_mat_type = $("#purchase_mat_type").val();
        $.ajax({
            type: "POST",
            url: hidden_base_url + "getMaterialStockByType",
            data: { mat_type: purchase_mat_type },
            dataType: "html",
            success: function (data) {
                $("#rmaterial").html(data);
            },
            error: function () {
                console.log("error");
            },
        });
    }); */
    $(document).on("change", "#purchase_mat_type", function () {
        let mat_type_id = $(this).find(":selected").val();
        let hidden_base_url = $("#hidden_base_url").val();
        $.ajax({
            type: "POST",
            url: hidden_base_url + "getMaterialCategory",
            data: { mat_type_id: mat_type_id },
            dataType: "json",
            success: function (data) {
                if(data) {
                    let category = data;
                    let select = $("#category");
                    select.empty();
                    select.append('<option value="">Please Select</option>');
                    category.forEach(function(item) {
                        if (item) {
                            let id = item.id;
                            let name = item.name;
                            select.append('<option value="' + id + '">' + name + '</option>');
                        }
                    });
                }                
            },
            error: function () {
                console.error("Failed to fetch product details.");
            },
        });
        $('#category').val("").trigger('change.select2');
    });

    $(document).on("change", "#category", function () {
        let cat_id = $(this).find(":selected").val();
        let mat_type = $("#purchase_mat_type").val();
        let hidden_base_url = $("#hidden_base_url").val();
        $.ajax({
            type: "POST",
            url: hidden_base_url + "getMaterialById",
            data: { mat_type: mat_type, id: cat_id},
            dataType: "json",
            success: function (data) {
                if(data) {
                    let materials = data;
                    let select = $("#rmaterial");
                    select.empty();
                    select.append('<option value="">Please Select</option>');
                    materials.forEach(function(item) {
                        if (item) {
                            let id = item.id;
                            let name = item.name;
                            let code = item.code;
                            let insert_type = item.insert_type;
                            select.append('<option value="' + id + '|' + name + ' (' + code + ')|' + name + '|' + insert_type + '">' + name + ' (' + code + ')</option>');
                        }
                    });
                }                
            },
            error: function () {
                console.error("Failed to fetch product details.");
            },
        });
        $('#rmaterial').val("").trigger('change.select2');
    });
    
    let hidden_alert = $("#hidden_alert").val();
    let hidden_cancel = $("#hidden_cancel").val();
    let hidden_ok = $("#hidden_ok").val();
    let input = document.getElementById("file_button");
    let default_currency = $("#default_currency").val();
    input.addEventListener("change", function () {
        let file = this.files[0];
        let img = new Image();
        //extension check jpg/jpeg/png/pdf/doc/docx only
        let ext = file.name.split(".").pop().toLowerCase();
        if (
            $.inArray(ext, ["jpg", "jpeg", "png", "pdf", "doc", "docx"]) == -1
        ) {
            $("#file_button").val("");
            $("#file_button").addClass("is-invalid");
            $(".errorFile").text(
                "Invalid file type. File type must be jpg, jpeg, png, pdf, doc or docx."
            );
        }

        //calculate image size
        let size = Math.round(Number(file.size) / 1024);
        //get width
        let width = Number(this.width);
        //get height
        let height = Number(this.height);
        if (Number(size) > 5120) {
            $("#file_button").val("");
            $("#file_button").addClass("is-invalid");
            $(".errorFile").text(
                "File size is too large. File size must be less than 5 MB."
            );
        }
        //call on load
        img.onload = function () {
            URL.revokeObjectURL(this.src);
            //calculate image size
            let size = Math.round(Number(file.size) / 1024);
            //get width
            let width = Number(this.width);
            //get height
            let height = Number(this.height);
            if (Number(size) > 5120) {
                $("#file_button").val("");
                $("#file_button").addClass("is-invalid");
                $(".errorFile").text(
                    "File size is too large. File size must be less than 5 MB."
                );
            }
        };

        let objectURL = URL.createObjectURL(file);
        img.src = objectURL;
    });

    $("#unit").select2({
        dropdownParent: $("#cartPreviewModal"),
    });

    $("#productModal").select2({
        dropdownParent: $("#multipleProductModal"),
    });

    $("#orderModal").select2({
        dropdownParent: $("#productionProductModal"),
    });

    $("#purchase_date").datepicker({
        format: "dd-mm-yyyy",
        autoclose: true,
        todayHighlight: true,
        // startDate: new Date()
    });
    $(document).on("change", "#unit", function (e) {
        let unit = $("#unit").val();
        let unit_name = $("#unit option:selected").text();
        $(".modal_unit_name").empty();
        $(".modal_unit_name").html(unit_name);
        $("#item_unit_modal").val(unit);
        $("#item_unit_name_modal").val(unit_name);
    });

    $(document).on("keydown", ".discount", function (e) {
        let keys = e.charCode || e.keyCode || 0;
        return (
            keys == 8 ||
            keys == 9 ||
            keys == 13 ||
            keys == 46 ||
            keys == 110 ||
            keys == 86 ||
            keys == 190 ||
            (keys >= 35 && keys <= 40) ||
            (keys >= 48 && keys <= 57) ||
            (keys >= 96 && keys <= 105)
        );
    });

    $(document).on("keyup", ".discount", function (e) {
        let input = $(this).val();
        let ponto = input.split(".").length;
        let slash = input.split("-").length;
        if (ponto > 2) $(this).val(input.substr(0, input.length - 1));
        $(this).val(input.replace(/[^0-9.%]/, ""));
        if (slash > 2) $(this).val(input.substr(0, input.length - 1));
        if (ponto == 2) $(this).val(input.substr(0, input.indexOf(".") + 4));
        if (input == ".") $(this).val("");
    });

    $("#supplierModal").on("hidden.bs.modal", function () {
        $(this).find("form").trigger("reset");
    });

    /**
     * @description This function is used to set the attribute of the element
     */
    function setAttribute() {
        let i = 1;
        $(".set_sn").each(function () {
            $(this).html(i);
            i++;
        });
        i = 1;
        $(".unit_price_c").each(function () {
            $(this).attr("id", "unit_price_" + i);
            i++;
        });
        i = 1;
        $(".qty_c").each(function () {
            $(this).attr("id", "qty_" + i);
            i++;
        });
        i = 1;
        $(".tax_percent_c").each(function () {
            $(this).attr("id", "tax_percent_" + i);
            i++;
        });
        i = 1;
        $(".tax_amount_c").each(function () {
            $(this).attr("id", "tax_amount_" + i);
            i++;
        });
        i = 1;
        $(".total_c").each(function () {
            $(this).attr("id", "total_" + i);
            i++;
        });
    }

    /**
     * @description This function is used to calculate the row
     */
    function cal_row111() {
        let i = 1;
        let row_total = 0;
        let row_total_sum = 0;
        let totalTax = 0;

        $(".unit_price_c").each(function () {
            // read unit price / qty (try id pattern first, fallback to row-relative)
            let unit_price = Number($("#unit_price_" + i).val());
            if (isNaN(unit_price)) {
                unit_price = parseFloat($(this).val()) || 0;
            }

            let qty = Number($("#qty_" + i).val());
            if (isNaN(qty)) {
                let $row = $(this).closest("tr");
                qty = parseFloat($row.find(".qty_c").val()) || parseFloat($row.find("input[name^='qty']").val()) || 0;
            }

            row_total = unit_price * qty;
            row_total_sum += row_total;

            // set row total (keeps your existing id pattern)
            if ($("#total_" + i).length) {
                $("#total_" + i).val(row_total.toFixed(2));
            } else {
                // fallback: find .total_c in the row
                $(this).closest("tr").find(".total_c").val(row_total.toFixed(2));
            }

            // --- per-row tax detection ---
            // 1) #tax_percent_X
            // 2) .tax_percent inside the same row
            // 3) fallback to global #tax_percent
            let taxPerVal = $("#tax_percent_" + i).length ? $("#tax_percent_" + i).val() : undefined;
            if (taxPerVal === undefined || $.trim(taxPerVal) === "") {
                let $rowTax = $(this).closest("tr").find(".tax_percent").first();
                if ($rowTax.length) taxPerVal = $rowTax.val();
                else taxPerVal = $("#tax_percent").val(); // global fallback
            }

            let taxPer = 0;
            if ($.trim(taxPerVal) === "" || isNaN(taxPerVal)) {
                taxPer = 0;
            } else {
                taxPer = parseFloat($.trim(taxPerVal));
            }

            let taxAmountForRow = row_total * (taxPer / 100);
            totalTax += taxAmountForRow;

            // set per-row tax amount if present (#tax_amount_X or .tax_amount_c)
            if ($("#tax_amount_" + i).length) {
                $("#tax_amount_" + i).val(taxAmountForRow.toFixed(2));
            } else {
                let $taxAmtField = $(this).closest("tr").find(".tax_amount_c");
                if ($taxAmtField.length) $taxAmtField.val(taxAmountForRow.toFixed(2));
            }

            i++;
        });
        let subtotal_with_tax = row_total_sum + totalTax;
        let paid = parseFloat($("#paid").val()) || 0;
        let other_amount = parseFloat($("#other").val()) || 0;
        $("#tax_amount").val(totalTax.toFixed(2));
        $("#subtotal").val(subtotal_with_tax.toFixed(2));
        $("#grand_total").val((row_total_sum + other_amount + totalTax).toFixed(2));
        let due = row_total_sum - paid + other_amount + totalTax;
        $("#due").val(due.toFixed(2));
    }
    function cal_row() {
        let i = 1;
        let row_total = 0;
        let row_total_sum = 0;
        let totalTax = 0;
        $(".unit_price_c").each(function () {
        // read unit price / qty (try id pattern first, fallback to row-relative)
        let unit_price = Number($("#unit_price_" + i).val());
        if (isNaN(unit_price)) {
            unit_price = parseFloat($(this).val()) || 0;
        }

        let qty = Number($("#qty_" + i).val());
        if (isNaN(qty)) {
            let $row = $(this).closest("tr");
            qty = parseFloat($row.find(".qty_c").val()) || parseFloat($row.find("input[name^='qty']").val()) || 0;
        }

        // base total
        let row_total = unit_price * qty;

        // --- per-row tax detection ---
        let taxPerVal = $("#tax_percent_" + i).length ? $("#tax_percent_" + i).val() : undefined;
        if (taxPerVal === undefined || $.trim(taxPerVal) === "") {
            let $rowTax = $(this).closest("tr").find(".tax_percent").first();
            if ($rowTax.length) taxPerVal = $rowTax.val();
            else taxPerVal = $("#tax_percent").val(); // global fallback
        }

        let taxPer = 0;
        if ($.trim(taxPerVal) === "" || isNaN(taxPerVal)) {
            taxPer = 0;
        } else {
            taxPer = parseFloat($.trim(taxPerVal));
        }

        // calculate per-row tax and total-with-tax
        let taxAmountForRow = row_total * (taxPer / 100);
        let row_total_with_tax = row_total + taxAmountForRow;

        // accumulate totals
        row_total_sum += row_total_with_tax;
        totalTax += taxAmountForRow;

        // set per-row tax amount if present
        if ($("#tax_amount_" + i).length) {
            $("#tax_amount_" + i).val(taxAmountForRow.toFixed(2));
        } else {
            let $taxAmtField = $(this).closest("tr").find(".tax_amount_c");
            if ($taxAmtField.length) $taxAmtField.val(taxAmountForRow.toFixed(2));
        }

        // ✅ update total to include tax
        if ($("#total_" + i).length) {
            $("#total_" + i).val(row_total_with_tax.toFixed(2));
        } else {
            $(this).closest("tr").find(".total_c").val(row_total_with_tax.toFixed(2));
        }

        i++;
    });

    // subtotal includes tax
    let subtotal_with_tax = row_total_sum + totalTax;
    let paid = parseFloat($("#paid").val()) || 0;
    let other_amount = parseFloat($("#other").val()) || 0;

    $("#tax_amount").val(totalTax.toFixed(2));
    $("#subtotal").val(row_total_sum.toFixed(2));
    $("#grand_total").val((row_total_sum + other_amount).toFixed(2));
    let due = row_total_sum - paid + other_amount;
    $("#due").val(due.toFixed(2));
    }

    /**
     * @description This function is used to print the invoice
     */
    function invoicePrint() {
        window.print();
    }

    $(document).on("keyup", ".invoicePrint", function (e) {
        invoicePrint();
    });

    $(document).on("click", ".invoicePrint", function (e) {
        invoicePrint();
    });

    $(document).on("focus", ".invoicePrint", function (e) {
        invoicePrint();
    });
    $(document).on("change", "#rmaterial", function (e) {
        let params = $(this).find(":selected").val();
        $("#qty_modal").val("");
        if (params != "") {
            let item_details_array = params.split("|");
            $("#item_id_modal").val(item_details_array[0]);
            $(".item_name_modal").html(item_details_array[1]);
            $("#item_name_modal").val(item_details_array[1]);
            $("#insert_type_modal").val(item_details_array[3]);
            // $("#item_currency_modal").val(item_details_array[5]);
            // $("#item_unit_modal").val(item_details_array[4]);
            // $(".modal_unit_name").html(item_details_array[4]);
            // $("#unit_price_modal").val(item_details_array[3]);
            $("#cartPreviewModal").modal("show");
        }
    });

    $(document).on("click", "#addToCart", function (e) {
        e.preventDefault();
        $(".rmError").remove();
        $(".unitErr").html("");
        $(".unitPriceErr").html("");
        $(".qtyErr").html("");
        let unit_price = $("#unit_price_modal").val();
        let qty_modal = $("#qty_modal").val();
        let item_unit_modal = $("#item_unit_modal").val();
        let item_unit_name_modal = $("#item_unit_name_modal").val();
        let item_name_modal = $("#item_name_modal").val();
        let item_id_modal = $("#item_id_modal").val();
        let item_currency_modal = $("#item_currency_modal").val();  
        let insert_type = $("#insert_type_modal").val(); 
        let insert_type_modal = ""; 
        if(insert_type!="" && insert_type==1) {
            insert_type_modal = "(Consumable)";
        } else if(insert_type!="" && insert_type==2){
            insert_type_modal = "(Non Consumable)";
        } else {
            insert_type_modal;
        }
        let hasError = false;
        if (!item_unit_modal) {
            $(".unitErr").html("Unit is required.");
            hasError = true;
        }
        if (!qty_modal) {
            $(".qtyErr").html("Quantity is required.");
            hasError = true;
        } else if (isNaN(qty_modal) || parseFloat(qty_modal) <= 0) {
            $(".qtyErr").html("Enter a valid quantity.");
            hasError = true;
        }
        if (!unit_price) {
            $(".unitPriceErr").html("Unit price is required.");
            hasError = true;
        } else if (isNaN(unit_price) || parseFloat(unit_price) < 0) {
            $(".unitPriceErr").html("Enter a valid unit price.");
            hasError = true;
        }
        if (hasError) {
            return false;
        }    
        appendCart(
            item_id_modal,
            item_name_modal,
            item_currency_modal,
            item_unit_modal,
            item_unit_name_modal,
            unit_price,
            qty_modal,
            insert_type_modal
        );
    });

    /**
     * @description This function is used to append the cart
     * @param {*} item_id_modal
     * @param {*} item_name_modal
     * @param {*} item_currency_modal
     * @param {*} item_unit_modal
     * @param {*} unit_price
     * @param {*} qty_modal
     * @returns
     */
    function appendCart(
        item_id_modal,
        item_name_modal,
        item_currency_modal,
        item_unit_modal,
        item_unit_name_modal,
        unit_price,
        qty_modal,
        insert_type_modal
    ) {
        let html =
            '<tr class="rowCount" data-id="' +
            item_id_modal +
            '">\n' +
            '<td class="width_1_p text-start"><p class="set_sn">1</p></td>\n' +
            "<td>" +
            '<input type="hidden" value="' +
            item_id_modal +
            '" name="rm_id[]"> ' +
            "<span>" +
            item_name_modal +
            "<br><small>"+ insert_type_modal +"</small></span></td>\n" +
            '<td><div class="input-group"><input type="number" tabindex="5" name="unit_price[]" onfocus="this.select();" class="check_required form-control integerchk input_aligning unit_price_c cal_row" placeholder="Unit Price" value="' +
            unit_price +
            '"></div><div class="text-danger d-none unitPriceErr"></div></td>' +
            '<td>' +
            '<input type="hidden" name="mat_unit[]" value="' + item_unit_modal + '">'+
            '<div class="input-group"><input type="number" data-countid="1" tabindex="51" id="quantity_amount_1" name="quantity_amount[]" onfocus="this.select();" class="check_required form-control integerchk input_aligning qty_c cal_row" value="' +
            qty_modal +
            '" placeholder="Quantity" ><span class="input-group-text">' +
            item_unit_name_modal +
            '</span></div><div class="text-danger d-none qtyErr"></div></td>' +
            '<td>' +
            '<input type="hidden" name="mat_unit[]" value="' + item_unit_modal + '">'+
            '<div class="input-group"><input type="number" data-countid="1" tabindex="51" id="tax_percent_1" name="tax_percent[]" onfocus="this.select();" class="check_required form-control integerchk input_aligning tax_percent_c cal_row" value="" placeholder="Tax Percentage" style="min-width: 120px;"></div><div class="text-danger d-none taxPerErr"></div></td>' +
            '<td>' +
            '<div class="input-group"><input type="number" data-countid="1" tabindex="51" id="tax_amount_1" name="tax_amount[]" onfocus="this.select();" class="check_required form-control integerchk input_aligning tax_amount_c cal_row" value="" placeholder="Tax Amount" style="min-width: 120px;" ></div><div class="text-danger d-none taxAmtErr"></div></td>' +
            '<td><div class="input-group mb-3"><input type="number" id="total_1" name="total[]" class="form-control input_aligning total_c" placeholder="Total" readonly="" style="min-width: 120px;"></div></td>"' +
            '<td class="ir_txt_center"><a class="btn btn-xs del_row dlt_button"><iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon> </a></td>\n' +
            "</tr>";

        let check_exist = true;

        $(".rowCount").each(function () {
            let id = $(this).attr("data-id");
            if (Number(id) == Number(item_id_modal)) {
                check_exist = false;
            }
        });

        $(".rm_id").each(function () {
            let id = $(this).val();
            if (Number(id) == Number(item_id_modal)) {
                check_exist = false;
            }
        });

        if (check_exist == true) {
            if (item_id_modal) {
                $(".add_tr").append(html);
                setAttribute();
                cal_row();
                $("#rmaterial").val("").change();
                $("#cartPreviewModal").modal("hide");
                return false;
            }
        } else {
            let hidden_alert = $("#hidden_alert").val();
            let hidden_cancel = $("#hidden_cancel").val();
            let hidden_ok = $("#hidden_ok").val();
            swal({
                title: hidden_alert + "!",
                text: "This Raw Material already added",
                cancelButtonText: hidden_cancel,
                confirmButtonText: hidden_ok,
                confirmButtonColor: "#3c8dbc",
            });
            $("#rmaterial").val("").change();
            $("#cartPreviewModal").modal("hide");
            return false;
        }
    }

    $(document).on("click", ".del_row", function (e) {
        $(this).parent().parent().remove();
        setAttribute();
        cal_row();
    });

    $(document).on("keyup", ".cal_row", function (e) {
        cal_row();
    });

    $(document).on("click", ".cal_row", function (e) {
        cal_row();
    });

    $(document).on("focus", ".cal_row", function (e) {
        cal_row();
    });

    $("#purchase_form").submit(function (e) {
        let status = true;
        let focus = 1;
        let reference_no = $("#reference_no").val();
        let supplier_id = $("#supplier_id").val();
        let date = $("#date").val();
        let statusField = $("#status").val();
        let purchase_mat_type = $("#purchase_mat_type").val();
        let paid = $("#paid").val();
        let accounts = $("#accounts").val();
        $("#purchase_mat_type").removeClass("is-invalid");
        $("#purchase_mat_type").closest("div").find(".text-danger").addClass("d-none").text("");
        if (!purchase_mat_type || purchase_mat_type.length === 0) {
            $("#purchase_mat_type").addClass("is-invalid");
            $("#purchase_mat_type").closest("div").find(".text-danger").removeClass("d-none").text("Please select at least one material type.");
            status = false;
        }
        /* if (reference_no == "") {
            showErrorMessage(
                "reference_no",
                "The Purchase No field is required."
            );
            status = false;
        } else {
            $("#reference_no").removeClass("is-invalid");
            $("#reference_no")
                .closest("div")
                .find(".text-danger")
                .addClass("d-none");
        } */

        if (supplier_id == "") {
            $("#supplier_id").addClass("is-invalid");
            let closestDiv = $(".supplierErr");
            closestDiv.text("The Supplier field is required");
            closestDiv.removeClass("d-none");
        } else {
            $("#supplier_id").removeClass("is-invalid");
            $(".supplierErr").addClass("d-none");
        }

        if (date == "") {
            showErrorMessage("date", "The Date field is required.");
            status = false;
        } else {
            $("#date").removeClass("is-invalid");
            $("#date").closest("div").find(".text-danger").addClass("d-none");
        }

        if (statusField == "") {
            showErrorMessage("status", "The Status field is required.");
            status = false;
        } else {
            $("#status").removeClass("is-invalid");
            $("#status").closest("div").find(".text-danger").addClass("d-none");
        }
        $(".unit_price_c").each(function () {
            let unit_price_c = $(this).val();
            let closestDiv = $(this).parent().next(".unitPriceErr");
            if (unit_price_c == "") {
                status = false;
                $(this).addClass("is-invalid");
                closestDiv.text("The Unit Price field is required");
                closestDiv.removeClass("d-none");
            } else {
                $(this).removeClass("is-invalid");
                closestDiv.addClass("d-none");
            }
        });
        $(".qty_c").each(function () {
            let qty_c = $(this).val();
            let closestDiv = $(this).parent().next(".qtyErr");

            if (qty_c == "") {
                status = false;
                $(this).addClass("is-invalid");
                closestDiv.text("The Quantity field is required");
                closestDiv.removeClass("d-none");
            } else {
                $(this).removeClass("is-invalid");
                closestDiv.addClass("d-none");
            }

            if (qty_c <= 0) {
                status = false;
                $(this).addClass("is-invalid");
                closestDiv.text("The Quantity field must be greater than 0");
                closestDiv.removeClass("d-none");
            } else {
                $(this).removeClass("is-invalid");
                closestDiv.addClass("d-none");
            }
        });
        let rowCount = $(".rowCount").length;        
        if (!Number(rowCount)) {
            $("#purchase_cart .add_tr").html(
                '<tr><td colspan="6" class="text-danger rmError">Please add minimum one Raw Material</td></tr>'
            );
            status = false;
        } else {
            $(".rmError").remove();
        }

        if (status == true) {
            return true;           
        } else {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            return false;
        }
    });

    function showErrorMessage(id, message) {
        $("#" + id + "").addClass("is-invalid");
        let closestDiv = $("#" + id + "")
            .closest("div")
            .find(".text-danger");
        closestDiv.text(message);
        closestDiv.removeClass("d-none");
    }

    setAttribute();
    cal_row();

    $("#pull_low_stock_products").on("click", function () {
        let hidden_base_url = $("#hidden_base_url").val();
        $.ajax({
            url: hidden_base_url + "getLowRMStock",
            method: "GET",
            success: function (response) {
                $(".add_tr").empty();
                $(".add_tr").append(response);
                $("#populate_click").val("clicked");
                setAttribute();
                cal_row();
            },
            error: function () {
                alert("error");
            },
        });
    });

    $("#supplier_id").on("change", function () {
        let hidden_base_url = $("#hidden_base_url").val();
        let supplier_id = $("#supplier_id").val();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: hidden_base_url + "getSupplierBalance",
            data: {
                supplier_id: supplier_id,
            },
            success: function (data) {
                $(".supplier_due").removeClass("d-none");
                $(".supplier_due").html(
                    `Balance: ${default_currency}${Math.abs(
                        data.supplier_balance
                    )}${
                        data.supplier_balance !== 0
                            ? ` (${
                                  data.supplier_balance < 0 ? "Debit" : "Credit"
                              })`
                            : ""
                    }`
                );
                $("input[name='supplier_due']").val(data.supplier_due);
                $(".supplier_credit_limit").val(data.credit_limit);
            },
        });
    });
    $("#rmaterial").on("change", function () {
        let rm_details = $("#rmaterial").val();
        if (rm_details != "") {
            let rm_details_array = rm_details.split("|");
            $("#unit_price_modal").val("");
            $("#cartPreviewModal").modal("show");
        }
    });

    $(document).on("click", ".low_stock", function (e) {
        let hidden_base_url = $("#hidden_base_url").val();
        $.ajax({
            url: hidden_base_url + "getLowRMStock",
            method: "GET",
            success: function (response) {
                if (response == "") {
                    console.log("No Low Stock Product Found");
                    swal({
                        title: hidden_alert + "!",
                        text: "No Low Stock Product Found",
                        cancelButtonText: hidden_cancel,
                        confirmButtonText: hidden_ok,
                        confirmButtonColor: "#3c8dbc",
                    });
                    return false;
                }
                $(".add_tr").empty();
                $(".add_tr").append(response);
                $("#populate_click").val("clicked");
                setAttribute();
                cal_row();
            },
            error: function () {
                console.log("error");
            },
        });
    });

    $(document).on("click", ".multi_product", function (e) {
        $(".addDataModalBody").html("");
    });

    $(document).on("change", "#rmaterialModal", function (e) {
        let params = $(this).find(":selected").val();
        if (params != "") {
            let item_details_array = params.split("|");

            let html = "";
            html += '<div class="row mpCart">';
            html += '<div class="col-md-4 d-flex align-items-end">';
            html +=
                '<p><span class="item_name_modal">' +
                item_details_array[2] +
                "(" +
                item_details_array[3] +
                ")</span></p>";
            html +=
                '<input type="hidden" id="item_id_modal" value="' +
                item_details_array[0] +
                '">';
            html +=
                '<input type="hidden" id="item_name_modal" value="' +
                item_details_array[1] +
                '">';
            html +=
                '<input type="hidden" id="item_currency_modal" value="' +
                item_details_array[5] +
                '">';
            html +=
                '<input type="hidden" id="item_unit_modal" value="' +
                item_details_array[4] +
                '">';
            html +=
                '<input type="hidden" id="item_unit_modal" value="' +
                item_details_array[4] +
                '">';
            html +=
                '<input type="hidden" id="item_currency_modal" value="' +
                item_details_array[5] +
                '">';
            html += "</div>";
            html += '<div class="col-md-4">';
            html += '<div class="form-group">';
            html += '<label for="unit_price_modal">Unit Price</label>';
            html +=
                '<input type="text" class="form-control" id="unit_price_modal" value="' +
                item_details_array[3] +
                '" readonly>';
            html += "</div>";
            html += "</div>";
            html += '<div class="col-md-4">';
            html += '<label class="custom_label">Quantity</label>';
            html += '<div class="input-group">';
            html +=
                '<input type="number" autocomplete="off" min="1" class="form-control integerchk1" name="qty_modal" id="qty_modal" placeholder="Quantity" value="1">';
            html +=
                '<span class="input-group-text">' +
                item_details_array[4] +
                "</span>";
            html += "</div>";
            html += "</div>";
            html += "</div>";
            $(".addDataModalBody").append(html);
        }
    });

    $(document).on("click", "#generate_purchase", function (e) {
        e.preventDefault();
        let productId = [];
        let quantity = [];

        $(".rowCount").each(function () {
            let id = $(this).find("input[name='multiple_product_id[]']").val();
            let qty = $(this).find("input[name='multiple_quantity[]']").val();
            productId.push(id);
            quantity.push(qty);
        });

        if (productId == "") {
            swal({
                title: hidden_alert + "!",
                text: "Please add at least one product",
                cancelButtonText: hidden_cancel,
                confirmButtonText: hidden_ok,
                confirmButtonColor: "#3c8dbc",
            });
            return false;
        }

        productId.forEach(function (item, index) {
            let hidden_base_url = $("#hidden_base_url").val();
            $.ajax({
                type: "POST",
                url: hidden_base_url + "getFinishProductRM",
                data: { id: item, value: quantity[index] },
                dataType: "json",
                success: function (data) {
                    $(".add_tr").append(data);
                    setAttribute();
                    cal_row();
                    $("#productModal").val("").change();
                    $("#qty_modal_product").val("");
                    $("#multipleProductModal").modal("hide");
                    $("#addToCartSec").addClass("d-none");
                    $("#cart_data").empty();
                },
                error: function () {
                    console.log("error");
                },
            });
        });
    });

    $(document).on("click", "#generate_purchase_from_production", function (e) {
        let customer_order_id = $("#orderModal").find(":selected").val();
        if (customer_order_id == "") {
            $("#orderModal").addClass("is-invalid");
            $(".orderErrMsg")
                .text("The Order field is required")
                .fadeIn()
                .delay(3000)
                .fadeOut();
        }

        if (customer_order_id != "") {
            let hidden_base_url = $("#hidden_base_url").val();
            let productId = [];
            $.ajax({
                type: "POST",
                url: hidden_base_url + "getCustomerOrderProducts",
                data: { id: customer_order_id, from: "purchase" },
                success: function (data) {
                    productId.push(data);
                    callAjaxForProductRm(productId);
                },
                error: function () {},
            });
        }
    });

    function callAjaxForProductRm(productId) {
        let hidden_base_url = $("#hidden_base_url").val();
        productId = productId[0];
        productId.forEach(function (item) {
            $.ajax({
                type: "POST",
                url: hidden_base_url + "getFinishProductRM",
                data: { id: item, value: 1 },
                dataType: "json",
                success: function (data) {
                    $(".add_tr").append(data);
                    setAttribute();
                    cal_row();
                    $("#orderModal").val("").change();
                    $("#productionProductModal").modal("hide");
                },
                error: function (error) {
                    console.log(error);
                },
            });
        });
    }

    $(document).on("click", ".add_to_cart_multiple_product", function (e) {
        let params = $("#productModal").val();
        let product_id = params.split("|")[0];
        let productName = params.split("|")[1];
        let quantity = $("#qty_modal_product").val();

        if (product_id == "") {
            $("#productModal").addClass("is-invalid");
            $(".productErr")
                .text("The Product field is required")
                .fadeIn()
                .delay(3000)
                .fadeOut();
        }

        if (quantity == "") {
            $("#qty_modal_product").addClass("is-invalid");
            $(".qtyErr")
                .text("The Quantity field is required")
                .fadeIn()
                .delay(3000)
                .fadeOut();
        }

        $(".rowCount").each(function () {
            let id = $(this).attr("data-id");
            if (Number(id) == Number(product_id)) {
                swal({
                    title: hidden_alert + "!",
                    text: "This Product already added",
                    cancelButtonText: hidden_cancel,
                    confirmButtonText: hidden_ok,
                    confirmButtonColor: "#3c8dbc",
                });
            }
        });
        let rowCount = Number($(".rowCount").length);
        if (product_id != "" && quantity != "") {
            let html = "<tr class='rowCount' data-id='" + product_id + "'>";
            html += "<td>" + (rowCount + 1) + "</td>";
            //hidden product id
            html +=
                "<input type='hidden' name='multiple_product_id[]' value='" +
                product_id +
                "'>";
            //hidden quantity
            html +=
                "<input type='hidden' name='multiple_quantity[]' value='" +
                quantity +
                "'>";
            html += "<td>" + productName + "</td>";
            html += "<td>" + quantity + "</td>";
            html +=
                '<td class="text-end"><a class="btn btn-xs del_row remove-tr dlt_button"><iconify-icon icon="solar:trash-bin-minimalistic-broken"></iconify-icon></a></td>';
            html += "</tr>";
            $("#addToCartSec").removeClass("d-none");
            $("#cart_data").append(html);
            $("#productModal").val("").change();
            $("#qty_modal_product").val("");
        }
    });

    $(document).on("click", ".dlt_button", function () {
        $(this).closest("tr").remove();
    });

    // Currency Change
    $(document).on("change", "#change_currency", function () {
        if ($(this).is(":checked")) {
            $("#currency_section").removeClass("d-none");
        } else {
            $("#currency_section").addClass("d-none");
        }

        $(".select2-container").css("width", "100%");
    });

    $(document).on("change", "#currency", function () {
        let data = $(this).val();
        let conversion_rate = data.split("|")[1];
        let amount = $("#paid").val();
        $("#converted_amount").val(currencyConversion(amount, conversion_rate));
        $(".converted_amount_currency").text(data.split("|")[2]);
        $("#currency_id").val(data.split("|")[0]);
    });

    function currencyConversion(amount, conversion_rate) {
        const convertedAmount = amount * conversion_rate;
        return convertedAmount.toFixed(2);
    }
});
