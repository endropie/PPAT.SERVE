<template>
<v-container  style="min-height:400px">
    <div class="card card-default" v-loading="SPA.index.loading">
        <div class="card-header">
            <div class="float-right">
                
                <v-btn @click="$router.push(`${SPA.resources.uri}/create`)" color="success" small> New </v-btn>
                <!-- <button class="btn btn-sm btn-primary text-bold text-white" data-toggle="collapse" data-target="#advancedFilter" aria-expanded="false" aria-controls="advancedFilter">Filter</button> -->
                <div class="btn-group">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu">
                        <button class="dropdown-item"  @click="showImport = true">Import</button>
                        <button class="dropdown-item"  @click="onExport">Export</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col">
                    <el-input placeholder="Please Enter to searching ..." v-model="SPA.index.request.search" class="" size="small">
                        <el-button slot="append" @click="handleSearch()" icon="el-icon-search" size="small" type="primary"></el-button>
                    </el-input>
                </div>
                <div class="col"> 
                    <el-pagination class="float-right" style="margin-right: -20px;" 
                        @size-change="handlePageSize" 
                        @current-change="handlePageCurrent"
                        :current-page.sync="SPA.index.pagenation.currentPage"
                        :page-sizes="SPA.index.pagenation.pageSizes"
                        :page-size="SPA.index.pagenation.pageSize"
                        :layout="'prev, pager, next, sizes'"
                        :total="SPA.index.pagenation.total">
                    </el-pagination> 
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="collapse" id="advancedFilter" style="border: dashed 1px #ffcc6f;background-color: #fffaf4;">
                        <el-form ref="formModeFilter" :model="SPA.index.request" label-position="top">
                            <div class="row m-3">
                                <el-form-item label="part_" prop="Part Number" class="col-md-3 col-sm-6 form-group mb-2">
                                    <el-input name="part_no" v-model="SPA.index.request.part_no" class="" size="small"></el-input>
                                </el-form-item>
                                <el-form-item label="Date Range" prop="date_range" class="col-md-3 col-sm-6 form-group mb-2">
                                    <el-date-picker name="date_range" type="daterange" align="right" size="small" range-separator="-" start-placeholder="Start date" end-placeholder="End date" 
                                        v-model="SPA.index.request.date_range" 
                                        unlink-panels value-format="yyyy-MM-dd"
                                        :picker-options="date_range_picker">
                                    </el-date-picker>
                                </el-form-item>
                            </div>
                        </el-form> 
                        <div class="col-12  mb-3 text-center">
                            <button class="btn btn-sm btn-warning text-bold text-white" @click="handleSearch(true)">Filter</button>
                            <button class="btn btn-sm btn-default text-bold" @click="resetForm('formModeFilter')">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 table-responsive" style="min-height:300px; min-width:690px">
                    <table class="table table-striped ">
                        <thead>
                            <tr>
                                <th width="40px"></th>
                                <th class="text-nowrap">No. Intern</th>
                                <th class="text-nowrap">Date</th>
                                <th class="text-nowrap">Time</th>
                                <th class="text-nowrap">Customer</th>
                                <th class="text-nowrap">Ref. Number</th>
                                <th class="text-nowrap">Ref. Date</th>
                                <th class="text-nowrap">No. Pickup</th>
                                <th class="text-nowrap">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in dataGrid" :key="index">
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-default " type="button" id="IndexAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="IndexAction">
                                            <button type="button" class="dropdown-item" @click="$router.push({path: `${SPA.resources.uri}/${item.id}` })"><i class="fas fa-eye  mr-2"></i> Info </button>
                                            <button type="button" class="dropdown-item" @click="$router.push({path: `${SPA.resources.uri}/${item.id}/edit`})"><i class="fas fa-edit  mr-2"></i> Edit </button>
                                            <button type="button" class="dropdown-item" @click="deleteEntry(item.id, index)"><i class="fas fa-trash  mr-2"></i> Delete</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap">{{ item.number }}</td>
                                <td class="text-nowrap">{{ item.date }}</td>
                                <td class="text-nowrap"> {{item.time}}</td>
                                <td class="text-nowrap"> {{item.customer.name}}</td>
                                <td class="text-nowrap"> {{item.ref_number}}</td>
                                <td class="text-nowrap"> {{item.ref_date}}</td>
                                <td class="text-nowrap"> {{item.pickup.name}}</td>
                                <td class="text-nowrap"> {{item.rate}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-3">
            </div>
        </div>
    </div>
</v-container>
</template>
<script>
    import Vue from 'vue';
    import SPAMix from '@/modules/mix-spa'

    export default {
        components:{
            //
        },
        mixins:[SPAMix],
        data: function () {
            
            return {
                api : [
                    {
                        id:1, 
                        number:'INM/2019-01/0001',
                        date: '2019-01-01',
                        time: '12:00:00',
                        ref_number: 'xxx',
                        ref_date: '2019-01-01',
                        rate:2,
                        pickup_id:1,
                        customer_id:1,
                        pickup:{
                            id:1,
                            name:'B 3882 SF'
                        },
                        customer:{
                            id:1,
                            name:'PT ABC'
                        },

                        inmaterial_items:[
                            {id:1, name:"Pre 1st"},
                            {id:2, name:"Pre 2nd"},
                            {id:3, name:"Pre 3th"},
                        ],
                    },
                    {
                        id:2, 
                        number:'INM/2019-01/0002',
                        date: '2019-01-01',
                        time: '11:34:00',
                        ref_number: 'xxx',
                        ref_date: '2019-01-05',
                        rate:2,
                        pickup_id:1,
                        customer_id:1,
                        pickup:{
                            id:1,
                            name:'B 6754 VSX'
                        },
                        customer:{
                            id:1,
                            name:'PT ABC'
                        },

                        inmaterials_items:[
                            {id:1, name:"Pre 1st"},
                            {id:2, name:"Pre 2nd"},
                            {id:3, name:"Pre 3th"},
                        ],
                    }

                ],
                showImport: false,
                date_range_picker: null,
                dataGrid: [],
                SPA :{ 
                  index : { 
                    request : 
                    {
                      date_range : '',
                      number : '',
                    } 
                  } 
                }
            }
        },
        created(){
            this.$route.meta.title = 'Warehouse - Incoming Materials'
            this.SPA.resources.api = '/api/v1/warehouses/incoming_goods'
            this.SPA.resources.uri = '/admin/warehouses/incoming_goods'

            
        },
        mounted() {
            
            this.routing()
        },
        watch:{
            '$route': 'routing',
        },
        methods: {
            setIndexPreparation(){
                let app = this
                let without = Array();
                if(app.$route.query.date_range)
                {
                    without['date_range'] = app.$route.query.date_range.split(',')
                }
                
                app.indexPreparation(without)
            },
            routing(){                
                let app = this;
                let params  = app.indexParameter(app.$route.query)

                app.SPA.index.loading = true
                
                // START DUMMY ==

                setTimeout(()=>{
                    console.log(app.api)
                    app.dataGrid = app.api
                    app.SPA.index.pagenation.currentPage = 1
                    app.SPA.index.pagenation.pageSize  = 20
                    app.SPA.index.pagenation.total     = app.dataGrid.length
                    
                    app.SPA.index.loading = false
                    
                    app.setIndexPreparation()
                }, 800);
                return false;

                // END DUMMY ==

                this.getAxios(`${this.SPA.resources.api}` + params)
                .then(function (res) {
                    app.dataGrid = res.data.data
                    app.SPA.index.pagenation.currentPage = Number(res.data.current_page)
                    app.SPA.index.pagenation.pageSize  = Number(res.data.per_page)
                    app.SPA.index.pagenation.total     = Number(res.data.total)
                    
                    app.SPA.index.loading = false
                    
                    app.setIndexPreparation()
                })
                .catch(function (error) {
                    app.onException(error)
                });
            },
            onExport(){
                let app = this;
                let params  = app.indexParameter( app.$route.query )
                // params  = app.indexFilterable();
                // console.log(`${this.SPA.resources.uri}/export` + params, '_blank');
                window.open(`${this.SPA.resources.uri}/export` + params, '_blank');
            },
            deleteEntry(id, index) {
                var app = this;

                app.$confirm('This will permanently delete the Item #'+id+'. Continue?', 'Warning', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then(() => {
                   console.log('Item Deleted')
                    // DUMMY
                    app.dataGrid.splice(index, 1);
                    return false;
                    // END DUMMY

                    axios.delete(app.SPA.resources.api +'/'+ id)
                    .then(function (resp) {
                        if(resp.data.success){
                            app.dataGrid.splice(index, 1);
                            app.$notify.success({title: 'Success', message: 'Item has been deleted.' });
                        }
                        else{
                            app.$notify.error({title: 'Error', message: 'Item Cannot delete.' });
                        }
                    })
                    .catch(function (resp) {
                        app.$alert("Could not delete this Item");
                    });
                })
                .catch(() => {});
            },
        }
    }
</script>