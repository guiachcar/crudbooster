@if($command=='layout')
    <div id='{{$componentID}}' class='border-box'>

        <div class="panel panel-default">
            <div class="panel-heading">
                [name]
            </div>
            <div class="panel-body table-responsive no-padding">
                [sql]
            </div>
        </div>

        <div class='action pull-right'>
            <a href='javascript:void(0)' data-componentid='{{$componentID}}' data-name='Small Box' class='btn-edit-component'><i class='fa fa-pencil'></i></a>
            &nbsp;
            <a href='javascript:void(0)' data-componentid='{{$componentID}}' class='btn-delete-component'><i class='fa fa-trash'></i></a>
        </div>
    </div>
@elseif($command=='configuration')
    <form method='post'>
        <input type='hidden' name='_token' value='{{csrf_token()}}'/>
        <input type='hidden' name='componentid' value='{{$componentID}}'/>
        <div class="form-group">
            <label>Name</label>
            <input class="form-control" required name='config[name]' type='text' value='{{@$config->name}}'/>
            
        </div>

        <div class="form-group">
            <label>SQL Query</label>
            <textarea name='config[sql]' rows="5" placeholder="E.g : select column_id,column_name from view_table_name"
                      class='form-control'>{{@$config->sql}}</textarea>
            <div class='help-block'>
                Make sure the sql query are correct unless the widget will be broken. Mak sure give the alias name each column. You may use alias [SESSION_NAME]
                to get the session. We strongly recommend that you use a <a href='http://www.w3schools.com/sql/sql_view.asp' target='_blank'>view table</a>
            </div>
        </div>

    </form>
@elseif($command=='showFunction')
    <?php
    if($key == 'sql') {
    try {
        $sessions = Session::all();
        foreach ($sessions as $key => $val) {
            $value = str_replace("[".$key."]", $val, $value);
        }
        $sql = DB::select(DB::raw($value));
    } catch (\Exception $e) {
        die('ERROR');
    }
                            
    ?>

    @if($sql)
        <p id="date_filter">
            <span id="date-label-from" class="date-label">Data Início: </span><input class="date_range_filter date" type="text" id="datepicker_from" />
            <span id="date-label-to" class="date-label">Data Fim: <input class="date_range_filter date" type="text" id="datepicker_to" />
        </p>
        <table id="table" class='table table-striped'>
            <thead>
            <tr>
                <?php $count_data=0;?>
                @foreach($sql[0] as $key=>$val)
                    @if(substr($key,0,5) == "data_")
                    <script>
                        var index_data= {{$count_data}};
                    </script>
                    @endif
                    <th>{{$key}}</th> 

                                       
                <?php $count_data++;?>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($sql as $row)
                <tr>
                    @foreach($row as $key=>$val)
                        <td>{{$val}}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    @foreach($sql[0] as $key=>$val)
                        <th>{{$key}}</th>
                    @endforeach
                </tr>
            </tfoot>
        </table>

        
       
     
        <script type="text/javascript">
            $('#table tfoot th').each( function () {
              var title = $(this).text();
              $(this).html( '<input type="text" placeholder="Filtrar '+title+'" />' );
            } );
            var oTable = $('table.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {extend:'copy',text:'Copiar'}, {extend:'csv',text:'CSV'}, {extend:'excel',text:'Excel'}, {extend:'pdf',text:'PDF'}, {extend:'print',text:'Imprimir'}
                ]
            });
            oTable.columns().every( function () {
                var that = this;
        
                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

         
            $("#datepicker_from").datepicker({
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                nextText: 'Proximo',
                prevText: 'Anterior',
                "onSelect": function(date) {
                    date = date.split(/\//)
                    minDateFilter = new Date([ date[1], date[0], date[2] ].join('/')).getTime();
                    oTable.draw();
                    oTable.draw();
                }
            }).keyup(function() {
                minDateFilter = new Date(this.value).getTime();
                
                this.text = minDateFilter;
            });

            $("#datepicker_to").datepicker({
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                nextText: 'Proximo',
                prevText: 'Anterior',
                "onSelect": function(date) {
                    date = date.split(/\//)
                    maxDateFilter = new Date([ date[1], date[0], date[2] ].join('/')).getTime();
                    console.log(maxDateFilter);
                    maxDateFilter = maxDateFilter + (24 * 60 * 60 * 1000);
                console.log(maxDateFilter);
                oTable.draw();
                    oTable.draw();
                }
            }).keyup(function() {
                maxDateFilter = new Date(this.value).getTime();
                oTable.draw();                
                this.text = maxDateFilter;
            }); 
            // Date range filter
            minDateFilter = "";
            maxDateFilter = "";

            $.fn.dataTableExt.afnFiltering.push(
                function(oSettings, aData, iDataIndex) {
                    if (typeof aData._date == 'undefined') {
                        var date_normal = aData[index_data].split(/\//);
                        aData._date = new Date ([ date_normal[1], date_normal[0], date_normal[2] ].join('/')).getTime();
                    }
                    
                            
                    if (minDateFilter && !isNaN(minDateFilter)) {
                        if (aData._date < minDateFilter) {
                            return false;
                        }
                    }

                    if (maxDateFilter && !isNaN(maxDateFilter)) {
                        if (aData._date > maxDateFilter) {
                            return false;
                        }
                    }
                    return true;
                }
            );
        </script>
@endif
<?php
}else {
    echo $value;
}
?>
@endif  