<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        <ul>
                            <li v-for="(user, index) in users" :key="index">
                                ({{index}}) {{user.name}} - {{user.email}} 
                            </li>
                        </ul>
                        <div class="card-body">
                            {{ brands || 'kosong'}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                users: [],
                brands: null,
            }
        },
        mounted() {
            console.log('Component mounted!', Echo)
            // Echo.join('chat')
            // .here( users => {
            //     this.users = users
            //     console.log('USER', users)
            // })
            // .joining((user) => {
            //     this.users.push(user)
            //     console.log(user.name);
            // })
            // .leaving((user) => {
            //     this.users = this.users.filter(x => x.id !== user.id)
            //     console.log(user.name);
            // });

            Echo.private('realtime')
            // .here( e => {
            //     console.log('EE', e)
            // })
            .listen('NewMessageNotification', (e) => {
                alert(e)
                console.log(e)
                this.brands = e
          });
        }
    }
</script>
