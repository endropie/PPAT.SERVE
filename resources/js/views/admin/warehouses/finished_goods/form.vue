<template>
<v-container class="p-0" grid-list-md >
  <div class="card card-primary" style="min-width:420px:overflow:">
    <div class="card-header" >
        <h4>{{ SPA.form.title || 'FORM' }}</h4> 
        <div class="float-right">
            
        </div>
    </div>
    <div class="card-body" >
        <v-form ref="mainForm" v-model="valid" lazy-validation> 
          <v-layout row>
            <v-flex md12 lg4>
                <v-layout row  class="mb-3">
                    <v-flex xs12>
                        <v-text-field label="Intern Number" v-model="rsForm.code"></v-text-field>
                    </v-flex>
                    <v-flex xs6>
                        <v-text-field label="Date" v-model="rsForm.date" type="date" :rules="ruleset('required', 'Date')" ></v-text-field>
                    </v-flex>
                    <v-flex xs6>
                        <v-text-field label="Time" v-model="rsForm.time" type="time" :rules="ruleset('required', 'Time')" ></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-radio-group v-model="rsForm.isDocument" row  :rules="ruleset('required')">
                            <v-radio label="Reguler" value="reguler"></v-radio>
                            <v-radio label="Return" value="return"></v-radio>
                        </v-radio-group>
                    </v-flex>
                    <v-flex xs6>
                        <v-text-field label="Number reference" v-model="rsForm.ref_number" :rules="ruleset('required', 'Ref. Number')"></v-text-field>
                    </v-flex>
                    <v-flex xs6>
                        <v-text-field label="Date reference" v-model="rsForm.ref_date" type="date" :rules="ruleset('required', 'Ref. Date')"></v-text-field>
                    </v-flex>
                </v-layout>
            </v-flex>
            <v-flex md6 lg4>
                <v-layout row  class="mb-3">
                    <v-flex xs12>
                        <v-autocomplete v-model="rsForm.customer_id" :items="optionData.customers" label="Select a Customer" 
                        item-value="id" item-text="name" flat small-chips
                        :rules="ruleset('required', 'Customer')" @change="onChangeCustomer"></v-autocomplete>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field label="Phone" v-model="rsForm.customer_phone"></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-textarea label="Address" v-model="rsForm.customer_address"></v-textarea>
                    </v-flex>
                </v-layout>
            </v-flex>
            <v-flex md6 lg4>
                <v-layout row  class="mb-3">
                    <v-flex xs12>
                        <v-text-field label="No. Pick-up" v-model="rsForm.pickup_id" ></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field label="Rate" v-model="rsForm.rate"></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        <v-textarea label="Description" v-model="rsForm.description"></v-textarea>
                     </v-flex>
                </v-layout>
            </v-flex>
          </v-layout>
          <v-layout row>
            <v-flex  xs12 class="table-responsive">
                <table class="table table-stripless itemrows">
                    <thead>
                        <tr>
                            <th class="" width="5%">&nbsp;</th>
                            <th class="" width="70px">Quantity</th>
                            <th class="" width="200px">Items number</th>
                            <th class="" width="120px">No. Part</th>
                            <th class="" width="120px">Part Name</th>
                            <th class="" width="70px">unit</th>
                            <th class="" width="120px">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(itemrow, index) in rsForm.finished_good_items" :key="index">
                            <td class="text-white text-center align-middle">
                                <span class="btn btn-sm btn-danger"  @click="removeEntry(index)"><i class="fas fa-trash"></i></span>
                            </td>
                            <td >
                                <v-text-field v-model="itemrow.quantity" :rules="ruleset('required')" single-line hide-details></v-text-field>
                            </td>
                            <td width="120px">
                                <v-autocomplete v-model="itemrow.item_id" :items="optionData.items" label="Select a Items"
                                  item-value="id" item-text="number" flat single-line hide-details small 
                                  :rules="ruleset('required')"
                                  @input="(val) => onChangeItem(val, index)"></v-autocomplete>
                            </td>
                            <td width="120px">
                                <v-text-field v-model="itemrow.part_number"  single-line hide-details readonly></v-text-field>
                            </td>
                            <td  width="200px" class="text-nowrap text-truncate">
                                <v-text-field v-model="itemrow.part_name"  single-line hide-details readonly></v-text-field>
                            </td>
                            <td width="120px">
                                <v-autocomplete v-model="itemrow.unit_id" :items="optionData.units" 
                                  item-value="id" item-text="name" flat single-line hide-details readonly></v-autocomplete>
                            </td>
                            <td width="180px">
                                <v-text-field v-model="itemrow.price"  single-line hide-details></v-text-field>
                            </td>
                        </tr>
                        <tr id="addItem">
                            <td class="text-white text-center align-middle">
                                <button type="button" @click="addNewEntry()" data-toggle="tooltip" title="" class="remove-item btn btn-sm btn-primary"><i class="fa fa-plus"></i>
                                </button>
                            </td>
                            <td colspan="4" class="text-right">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </v-flex>
          </v-layout>
          <v-layout row>
            <v-flex xs12>
                <v-textarea label="Description" v-model="rsForm.description"></v-textarea>
            </v-flex>
          </v-layout>
        
        </v-form>
    </div>
    <div class="card-footer" >
         <v-btn @click="cancelForm()" color="secondary" small> Cancel </v-btn>
         <v-btn @click="resetForm('mainForm')" color="default" small> Reset </v-btn>
         <v-btn @click="saveForm('mainForm')" color="success" small> Save </v-btn>
    </div>
  </div>
</v-container>
</template>

<script>
    import SPAMix from '@/modules/mix-spa'
    
    export default {
        mixins:[SPAMix],
        components: {},
        data () { 
            return {
                valid:false,
                api: {
                    id:1, 
                    number:'INM/2019-01/0001',
                    date: '2019-01-01',
                    time: '12:00:00',
                    ref_number: 'xxx',
                    ref_date: '2019-01-01',
                    customer_id:1,
                    customer_phone:'021-90887612',
                    customer_address:'Jl. Melati No. 90 \nTambun kab. Bekasi \nJawa Barat 51781',
                    customer:{
                        id:1,
                        name:'PT ABC',
                        phone:'021-90887612',
                        address_raw:'Jl. Melati No. 90 \nTambun kab. Bekasi \nJawa Barat 51781',
                    },

                    finished_good_items:[
                      {
                        id:1, 
                        item_id:1,
                        number:'KD02',
                        part_no:"part2",
                        part_mtr:"MTR 22",
                        part_fg:"FG 222",
                        quantity: 5,
                      },
                      {
                        id:2, 
                        item_id:3,
                        quantity: 10,
                      }
                    ],
                },
                rsForm: {},
                errors: {},
                optionData:{
                    accounts: {},
                },
                optionSearch:{
                    specification_id:'',
                },
                modalForm: {},
            }
        },
        created(){
            this.SPA.resources.api = '/api/v1/warehouses/finished_goods'
            this.SPA.resources.uri = '/admin/warehouses/finished_goods'
            this.$route.meta.title  = 'Warehouse - Finished Materials'
            if(this.$route.meta.mode == 'edit'){
                this.SPA.form.title     = 'FORM EDIT #' + this.$route.params.id
            }else{
                this.SPA.form.title     = 'FORM ADD NEW'
            }  

            this.routing()
            
            // Get Fetch All Data
            this.optionData.customers = [
                {id:1, name:"PT. ABC", phone:'021-90887612', address_raw:'Jl. Melati No. 90 \nTambun kab. Bekasi \nJawa Barat 51781' },
                {id:2, name:"PT. DEF", phone:'021-88227738', address_raw:'Jl. Pati Mas No. 82 RT 08/09 \nTambun kab. Bekasi \nJawa Barat 51376' },
                {id:3, name:"PT. FGH", phone:'021-90885299', address_raw:'Jl. Taman Bunga Bungur\nKomplek Industri No68 Blok F5 \nCisarua kab. Bogor \nJawa Barat 52354' },
            ];

            this.optionData.items = [
                {
                    id:1, 
                    number: 'KD01',
                    part_number:"Part 1",
                    part_mtr:"MTR 11",
                    part_fg:"FG 111",

                    customer_id:1,
                    specification_id:1,
                    atpm_id:1,

                    order_number:1,
                    item_date: '2019-01-01',
                    item_time: '12:00:00',

                    pre_productions:[
                        {id:1, name:"Pre 1st"},
                        {id:2, name:"Pre 2nd"},
                        {id:3, name:"Pre 3th"},
                    ],
                    sa_treatments:[
                        {id:1, name:"Treatment 1st"},
                        {id:4, name:"Treatment 4th"},
                        {id:5, name:"Treatment 5th"},
                    ],

                    packing_time:'12:00:00',
                    sa_area:'test',
                    price: 20000.00,
                    price_brl: 20000.00,
                    price_dm: 20000.00,
                    category_id:1,
                    ordertype_id:1,
                    marketplace_id:1,
                    unit_id:1,
                    picture:null,
                    enable:true,
                },
                {
                    id:2, 
                    number: 'KD02',
                    part_number:"Part 2",
                    part_mtr:"MTR 22",
                    part_fg:"FG 222",

                    customer_id:1,
                    specification_id:1,
                    atpm_id:1,

                    order_number:1,
                    item_date: '2019-01-01',
                    item_time: '12:00:00',

                    pre_productions:[
                        {id:1, name:"Pre 1st"},
                        {id:2, name:"Pre 2nd"},
                        {id:3, name:"Pre 3th"},
                    ],
                    sa_treatments:[
                        {id:1, name:"Treatment 1st"},
                        {id:4, name:"Treatment 4th"},
                        {id:5, name:"Treatment 5th"},
                    ],

                    packing_time:'12:00:00',
                    sa_area:'test',
                    price: 20000.00,
                    price_brl: 20000.00,
                    price_dm: 20000.00,
                    category_id:1,
                    ordertype_id:1,
                    marketplace_id:1,
                    unit_id:1,
                    picture:null,
                    enable:true,
                },
                {
                    id:3, 
                    number: 'KD03',
                    part_number:"Part 3",
                    part_mtr:"MTR 33",
                    part_fg:"FG 333",

                    customer_id:1,
                    specification_id:1,
                    atpm_id:1,

                    order_number:1,
                    item_date: '2019-01-01',
                    item_time: '12:00:00',

                    pre_productions:[
                        {id:1, name:"Pre 1st"},
                        {id:2, name:"Pre 2nd"},
                        {id:3, name:"Pre 3th"},
                    ],
                    sa_treatments:[
                        {id:1, name:"Treatment 1st"},
                        {id:4, name:"Treatment 4th"},
                        {id:5, name:"Treatment 5th"},
                    ],

                    packing_time:'12:00:00',
                    sa_area:'test',
                    price: 20000.00,
                    price_brl: 20000.00,
                    price_dm: 20000.00,
                    category_id:1,
                    ordertype_id:1,
                    marketplace_id:1,
                    unit_id:1,
                    picture:null,
                    enable:true,
                },
            ];

            this.optionData.units = [
                {id:1, code:"Pcs", name:"Packs"},
                {id:2, code:"Kg", name:"Kilo Gram"},
                {id:3, code:"Ltr", name:"Liter"},
                {id:3, code:"Brl", name:"Barel"},
                {id:3, code:"Set", name:"Set"},
            ];
            
        },
            
        mounted() {
            //
        },
        watch:{
            '$route' : 'routing'
        },
        computed: {
            datamap_items(){
                if(!this.optionData.items) return [];
                return this.optionData.items.reduce(function(map, obj) {
                    map[obj.id] = obj;
                    return map;
                }, {});
            },
            datamap_customers(){
                if(!this.optionData.items) return [];
                return this.optionData.customers.reduce(function(map, obj) {
                    map[obj.id] = obj;
                    return map;
                }, {});
            },
        },
        methods: {
            routing(){
                let app = this
                
                app.SPA.form.loading = true;


                // START DUMMY

                setTimeout(()=>{
                    app.setData(app.api)

                    app.SPA.form.loading = false;
                }, 800);
                return false;

                // END DUMMY

                let urls = this.apiResourcesForm(this.$route)
                this.getAxios(urls).then((res) => { 
                    app.setData(res)
                    app.SPA.form.loading = false;
                }).catch(function (error) {
                    app.onException(error)
                });
            },
            setData(res) {
                if(this.$route.meta.mode === 'edit') {
                    // if(res.data.isForm.edit == false){
                    //     this.$alert('Ops.. Data cant to update. \nThis data has been realations', 'Orders - Edit', { 
                    //         confirmButtonText: 'OK', 
                    //         callback: action => {
                    //             history.back(-1)
                    //         }
                    //     });
                    //     return false;
                    // }  
                }
                else{
                    res = {
                        finished_good_items:[],
                    };
                }
                
                this.rsForm = res;

                if(this.rsForm.finished_good_items.length == 0) {
                    this.addNewEntry()
                }
                
                this.SPA.form.show = true
                //this.$bar.finish()
            },
            onChangeItem(id, index){
                if(id)
                {
                    if(this.datamap_items[item_id]){
                        this.rsForm.finished_good_items[index].number      = this.datamap_items[id].number
                        this.rsForm.finished_good_items[index].part_number = this.datamap_items[id].part_number
                        this.rsForm.finished_good_items[index].part_name   = [this.datamap_items[id].part_mtr, this.datamap_items[id].part_fg].join('/')
                        this.rsForm.finished_good_items[index].unit_id     = this.datamap_items[id].unit_id
                        this.rsForm.finished_good_items[index].price     = this.datamap_items[id].price
                    }
                }
                else{
                        this.rsForm.finished_good_items[index].number      = null
                        this.rsForm.finished_good_items[index].part_number = null
                        this.rsForm.finished_good_items[index].part_name   = null
                        this.rsForm.finished_good_items[index].unit_id     = null
                        this.rsForm.finished_good_items[index].price       = null
                }
            },
            onChangeCustomer(id){
                if(id)
                {
                    if(this.datamap_customers[id]){
                        this.rsForm.customer_phone   = this.datamap_customers[id].phone
                        this.rsForm.customer_address = this.datamap_customers[id].address_raw
                    }
                }
                else{
                        this.rsForm.customer_phone   = null
                        this.rsForm.customer_address = null
                }
            },
            addNewEntry(){
                var newEntri = {id:null};
                
                this.rsForm.finished_good_items.push(newEntri)

                Vue.nextTick(() => {
                    let elements = $("input[type!='hidden']").filter("[name^='finished_good_items']").filter("[name*='account_id']")
                    elements.each(function(index, el){

                        if(index === elements.length - 1){
                            el.focus();
                        }
                        
                    })
                })
            },
            removeEntry(index) {
                this.rsForm.finished_good_items.splice(index, 1)
                if(this.rsForm.finished_good_items.length < 1) this.addNewProduction()
            },
            saveForm(formName) {
                var app = this;
                app.$refs[formName].validate((valid) => {
                    if (valid) 
                    {
                        if(app.totalDebit != app.totalCredit ){
                            app.$message.error('Oops, Submit Failed. Total Credit must be same with Total Debit.!!')
                            return false;
                        }else if(app.totalDebit == 0 || app.totalCredit == 0){
                            app.$message.error('Oops, Submit Failed. Total Credit or Debit must be more than 0 !!')
                            return false;
                        }

                        app.$alert('Sorry, this App is Undercontruction!');

                        // this.onSubmitForm(formName)
                                                
                    } else {
                        
                        app.$message.error('Oops, Submit Error.');
                        return false;
                    }
                });
            },
            
        }
    }
</script>

<style>
.itemrows tbody td{
    padding: 2px 4px;
}
.itemrows .v-text-field {
    padding-top: 2px;
    margin-top: 4px;
}
</style>
