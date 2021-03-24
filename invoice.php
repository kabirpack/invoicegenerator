<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management Records</title>
    
    <!--External StyleSheets -->
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-PMjWzHVtwxdq7m7GIxBot5vdxUY+5aKP9wpKtvnNBZrVv1srI8tU6xvFMzG8crLNcMj/8Xl/WWmo/oAP/40p1g==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
    <!-- External StyleSheets -->


   <style>

   </style>
</head>
<body>
    <center>
      <h2 style="margin-top: 3%;">Invoice Generator</h2>
    </center>


    <form method="POST" id="inv_mainform">


        <div class="container" style="padding:10px 10px;">
            <fieldset id="field" class="scheduler-border responsive-fieldset">
                <legend class="scheduler-border">Invoice Details</legend>
                    <div class="row">
                        <div class="container" id="ans_usage">
                            <div class="row justify-content-around">
                                <div class="col-2">
                                    <label>Name</label>
                                </div>
                                <div class="col-2">
                                    <label>Quantity</label>
                                </div>
                                <div class="col-2">
                                    <label>Unit Price($)</label>
                                </div>
                                <div class="col-2">
                                    <label>Tax</label>
                                </div>
                                <div class="col-2">
                                    <label>Amount</label>
                                </div>
                            </div>
                            <div id="inv_list_pop">
                            <!-- new row comes here -->
                            </div>
                        </div>

                    </div>

            </fieldset>


        </div>


            <div class="form-group row justify-content-center">
                <center>
                    <button type="button" class="btn btn-warning" id="inv_new"><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;Add Item</button>
                    <button class="btn btn-danger resetform" type="button"><i class="fa fa-repeat" aria-hidden="true"></i>&nbsp;Reset</button>
                </center>
            </div>


        <div class="container" style="padding:10px 10px;">
            <fieldset id="field" class="scheduler-border responsive-fieldset">
                <legend class="scheduler-border">Total</legend>
                    <div class="row">
                        <div class="container" id="">
                            <div class="row justify-content-around">
                                    <div>
                                    <label>Total without Tax</label>
                                    <input  type="number" step="0.01" id="subtotal_tax" name="" class="form-control">
                                    </div>
                                    <div>
                                    <label>Total with Tax</label>
                                    <input  type="number" step="0.01"  id="subtotal" name="" class="form-control">
                                    </div>
                                    <div>
                                    <label>Discount by amount</label>
                                    <input  type="number" step="0.01" id="disby_amt" name="" onchange="discount('amt')" class="form-control">
                                    </div>
                                    <div>
                                    <label>Discount by percentage</label>
                                    <input  type="number" step="0.01" id="disby_percent" name="" onchange="discount('per')" class="form-control">
                                    </div>

                            </div>
                        </div>

                    </div>

            </fieldset>


        </div>

        
        
        


        <div class="form-group row justify-content-center">
            <center>
                <button id="ans_save" type="submit" name="submit" class="btn btn-success"><i class="fa fa-save"></i> &nbsp;&nbsp;Generate Invoice</button>&nbsp;&nbsp;&nbsp;
            </center>
        </div>
        <center>
        <div class="col-md-6">
        <table id="example" class="table table-bordered table-striped table-hover table-responsive-stack" width="100%"></table>
        </div>
        <center>
    </form>

    <!-- External Scripts -->
        <script src="./assets/js/jquery.min.js"></script> 
        <script src="./assets/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
    <!-- External Scripts -->


<script>
var item_content="";
var item_count=1;
var amount = 0;
var sub_total = 0;
var sub_tax_total = 0;
var qty = 0;
var price = 0;
var dis_amt = 0;
var dis_percent = 0;
var invoice_amt = 0;

$(".resetform").on("click",function(){
            $('#inv_mainform').trigger('reset');
            $('#inv_list_pop').empty();
            if ($.fn.DataTable.isDataTable("#example")) {
                                            console.log('It is data table');
                                
                                $("#example").DataTable().clear().destroy();
                                        }
                                $("#example").html("");

        });


$("#inv_mainform").on("submit", function(event){

            event.preventDefault();
            console.log('Inside Submit');
            var datapass=$('#inv_mainform').serializeArray();
            console.log(JSON.stringify(datapass));
            var n = datapass.length;
            var allValues = [];
            var oneArray = [];

            for(var i=0;i<n;i++){
            const value = datapass[i]['value'];
            allValues.push(value)
            }
            const dataTableData = chunk(allValues,5);
            if ($.fn.DataTable.isDataTable("#example")) {
                                            console.log('It is data table');
                                
                                $("#example").DataTable().clear().destroy();
                                        }
                                $("#example").html("");

            $('#example').DataTable( {
        data: dataTableData,
        dom: 'Bfrtip',
        columns: [
            { title: "Name" },
            { title: "Quantity" },
            { title: "price($)" },
            { title: "Tax%" },
            { title: "Amount($)" },
        ],
        buttons: [  
            {
            extend: 'print',
            //className: "buttonsToHide",
            attr: { id: 'printtabl' },
            title:'<center>Invoice record</center>',
            messageTop: '<h1 align = "right" style = >Total Amount:$'+sub_tax_total+'<h1>',

            // customize: function ( win ) {
            // $(win.document.body)
            //             .css('align-center' ) 
            //         $(win.document.body).find( 'table' )
            //             .addClass( 'compact' )
            //             .css( 'font-size', 'inherit','align-center');
            //     }
            
            }                  
        ]

    } );




});
$(function(){
    item_content='<div class="row justify-content-around" id="item_'+item_count+'" "inv><div class="col-2"><input type="text" id="item_name_'+item_count+'" name="name_'+item_count+'" class="form-control"></div><div class="col-2"><input onchange="change_total_tax('+item_count+')" type="number" id="item_qty_'+item_count+'" name="qty_'+item_count+'" class="form-control"></div><div class="col-2"><input onchange="change_total_tax('+item_count+')" type="number" step="0.01" id="item_price_'+item_count+'" name="price_'+item_count+'" onfocusout="calculate_amt('+item_count+')" class="form-control"></div><div class="col-2 input-group-prepend"><select class="form-control" id="item_tax_'+item_count+'" name="price_'+item_count+'" onchange="calculate_tax('+item_count+')"><option value="0">0%</option><option value="1">1%</option><option value="5">5%</option><option value="10">10%</option></select></div><div class="col-2"><input type="number" step="0.01" id="item_amt_'+item_count+'" name="amt_'+item_count+'" class="form-control"></div><br><br>';
        item_count++;
        $('#inv_list_pop').append(item_content);
});

$("#inv_new").on("click",function(){
        item_content='<div class="row justify-content-around" id="item_'+item_count+'" "inv><div class="col-2"><input type="text" id="item_name_'+item_count+'" name="name_'+item_count+'" class="form-control"></div><div class="col-2"><input onchange="change_total_tax('+item_count+')" type="number" id="item_qty_'+item_count+'" name="qty_'+item_count+'" class="form-control"></div><div class="col-2"><input onchange="change_total_tax('+item_count+')" type="number" step="0.01" id="item_price_'+item_count+'" name="price_'+item_count+'" onfocusout="calculate_amt('+item_count+')" class="form-control"></div><div class="col-2 input-group-prepend"><select class="form-control" id="item_tax_'+item_count+'" name="price_'+item_count+'" onchange="calculate_tax('+item_count+')"><option value="0">0%</option><option value="1">1%</option><option value=5>5%</option><option value="10">10%</option></select></div><div class="col-2"><input type="number" step="0.01" id="item_amt_'+item_count+'" name="amt_'+item_count+'" class="form-control"></div><br><br>';
        item_count++;
        $('#inv_list_pop').append(item_content);
});

function remove_item(count){
$("#item_"+count).remove();
$("#field").removeClass("scheduler-border");
$("#field").addClass("scheduler-border")
//needs to be improved
}

function calculate_amt(id){
amount = 0;
qty = 0;
price = 0;
if($("#item_qty_"+id).val()){
    if($("#item_price_"+id).val()){
        qty = $("#item_qty_"+id).val();
        price = $("#item_price_"+id).val()
        amount = qty*price;
        $("#item_amt_"+id).val(amount);
        sub_total = sub_total + amount;
        $("#subtotal_tax").val(sub_total);
        if(($("#item_tax_"+id).val()) == "0"){
            $("#item_amt_"+id).val(amount);
            sub_tax_total = sub_tax_total +  amount;
            $("#subtotal").val(sub_tax_total);

            }

        if(($("#item_tax_"+id).val()) ==  "1"){

            amount = amount + (amount/100)*1;
            $("#item_amt_"+id).val(amount);
            sub_tax_total = sub_tax_total+ amount;
            $("#subtotal").val(sub_tax_total);

            }
        if(($("#item_tax_"+id).val()) ==  "5"){

            amount = amount + (amount/100)*5;
            $("#item_amt_"+id).val(amount);
            sub_tax_total = sub_tax_total + amount;
            $("#subtotal").val(sub_tax_total);

            }
        if(($("#item_tax_"+id).val()) ==  "10"){

            amount = amount + (amount/100)*10;
            $("#item_amt_"+id).val(amount);
            sub_tax_total = sub_tax_total + amount;
            $("#subtotal").val(sub_tax_total);

        }


    }
}
}

function calculate_tax(id){
    qty = $("#item_qty_"+id).val();
    price = $("#item_price_"+id).val()
    amount = $("#item_amt_"+id).val();
    sub_tax_total = sub_tax_total - amount;
    amount = qty*price;


    if(($("#item_tax_"+id).val()) == "0"){
    $("#item_amt_"+id).val(amount);
    sub_tax_total = sub_tax_total +  amount;
    $("#subtotal").val(sub_tax_total);

    }

    if(($("#item_tax_"+id).val()) ==  "1"){

        amount = amount + (amount/100)*1;
        $("#item_amt_"+id).val(amount);
        sub_tax_total = sub_tax_total+ amount;
        $("#subtotal").val(sub_tax_total);

    }
    if(($("#item_tax_"+id).val()) ==  "5"){

        amount = amount + (amount/100)*5;
        $("#item_amt_"+id).val(amount);
        sub_tax_total = sub_tax_total + amount;
        $("#subtotal").val(sub_tax_total);

    }
    if(($("#item_tax_"+id).val()) ==  "10"){

    amount = amount + (amount/100)*10;
    $("#item_amt_"+id).val(amount);
    sub_tax_total = sub_tax_total + amount;
    $("#subtotal").val(sub_tax_total);

    }



}


function change_total_tax(id){
    if($("#item_amt_"+id).val()){
        amount = $("#item_amt_"+id).val();
        qty = $("#item_qty_"+id).val();
        price = $("#item_price_"+id).val()
        tax = parseInt($("#item_tax_"+id).val());
        console.log("tax",tax);
        sub_total = Math.round(sub_total - (amount-(amount/100)*tax));
        sub_tax_total = sub_tax_total - amount; 
    }

}

function discount(atr){
    // console.log(sub_tax_total);
    if(atr == "amt"){
        dis_amt = $("#disby_amt").val();
        sub_tax_total = Math.round(sub_tax_total - dis_amt);
        $("#subtotal").val(sub_tax_total);

    }
    if(atr == "per"){
        dis_percent = $("#disby_percent").val();
        sub_tax_total =Math.round(sub_tax_total - (sub_tax_total/100)*dis_percent);
        $("#subtotal").val(sub_tax_total);

    }

}

function chunk(arr, chunkSize) {
  if (chunkSize <= 0) throw "Invalid chunk size";
  var R = [];
  for (var i=0,len=arr.length; i<len; i+=chunkSize)
    R.push(arr.slice(i,i+chunkSize));
  return R;
}








</script>



</body>