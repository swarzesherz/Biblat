/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 a(b){2 c=\'\';4(2 1=0;1<b.5;1++){c+=\'\'+b.6(1).7(8)}9 c}',13,13,'|i|var|function|for|length|charCodeAt|toString|16|return|||'.split('|'),0,{}));
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('4 b(a){3 a=a.5();3 c="";6(3 1=0;1<a.7;1+=2)c+=8.9(d(a.e(1,2),f));g c}',17,17,'|i||var|function|toString|for|length|String|fromCharCode||||parseInt|substr|16|return'.split('|'),0,{}));


class_met = {
    cons:{
        DISCOVERY_DOCS: ["https://sheets.googleapis.com/$discovery/rest?version=v4"],
        SCOPES: ['https://www.googleapis.com/auth/spreadsheets', 'https://www.googleapis.com/auth/drive'],
    },
    var:{
        values:[]
    },
    ready: function(){
        var object = {
                private_key: env.P_K,
                client_email: b(env.C_E),
                scopes: class_met.cons.SCOPES,
            };
        gapi.load("client", async function(){
                    gapi.auth.setToken(await GetAccessTokenFromServiceAccount.do(object));
                    gapi.client.init({
                        discoveryDocs: class_met.cons.DISCOVERY_DOCS,
                    }).then(function () {
                        //Lectura de hoja de cálculo, se requiere el ID y la hoja de la que leerá
                        gapi.client.sheets.spreadsheets.values.get({
                            spreadsheetId: b(env.sId),
                            range: "Bitacora",
                        }).then(function(res) {
                            class_met.var.values = [];
                            $.each(res.result.values, function(i,val){
                                var obj={};
                                if(i > 0){
                                    $.each(res.result.values[0], function(i2, val2){
                                        obj[i2] = val[i2];
                                    });
                                    class_met.var.values.push(obj);
                                }
                            });
                            data_filter = [];
                            data_filter = class_utils.filterdiff_prop(class_met.var.values, '21', ['', null, undefined]);
                            data_filter = class_utils.unique_arr(data_filter, [0,2,3,4]);
                            data_filter = class_utils.filterdiff_prop(data_filter, '3', ['132.248.9.97']);
                            
                            paises = [];
                            dataTree = [];
                            $.each(data_filter, function(i,val){
                                if(paises.indexOf(val[4]) !== -1 && paises.indexOf(val[4]) !== undefined){
                                    dataTree[paises.indexOf(val[4])]['value']++;
                                }else{
                                    var obj={};
                                    obj['id'] = val[4];
                                    obj['parent'] = '';
                                    obj['name'] = val[4];
                                    obj['value'] = 1;
                                    obj['color'] = class_utils.getRandomColor();
                                    paises.push(val[4]);
                                    dataTree.push(obj);
                                }
                            });
                            class_met.chart_treemap(dataTree);
                            //data_filter = class_utils.filter_prop_arr(data_filter, 'id_tipo_info', class_cp.v.informacion);
                            //data_filter = class_utils.filter_prop_arr(data_filter, 'id_recurso', class_cp.v.tipo2);
                        });
                    });
        });
    },
    chart_treemap: function(data){
        grafica = JSON.parse(JSON.stringify(class_utils.chartTreemap));
        grafica.chart.height = (window.innerHeight/2);
        grafica.chart.width = (window.innerWidth/2);
        grafica.title.text = 'Procedencia de las consultas';
        grafica.tooltip.pointFormatter = function(){
            return this.name + ': ' + this.value;
        }
        grafica.series[0].data = data;
        Highcharts.chart('container', grafica);
    }
};

$(class_met.ready);
