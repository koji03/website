<template>
<ul class="c-nav">
    <li class="c-nav__item" v-on:click='panelFlagChange'>
        <i class="fas fa-user-circle c-nav__icon"></i>
        <div class="c-nav__panel" v-if="Panel">
            <button  @click="logout" class="c-nav__panel__action" >Logout</button>
        </div>
    </li>            
    <div v-if="modalShow">
        <scheduledHome/>
    </div>
</ul>

</template>
<script>
import { mapState } from 'vuex'
import scheduledHome from '../ScheduledComponents/scheduledHome'
export default {
    data(){
        return{
            Panel:false,
        }
    },
    components:{
            scheduledHome
    },
    methods:{
        async logout () {
            await this.$store.dispatch('auth/logout')
            if (this.apiStatus) {
                this.$router.push('/form/login')
            }
        },
        panelFlagChange(){
            this.Panel = !this.Panel
        },
    },
    computed: {
        ...mapState({
        apiStatus: state => state.auth.apiStatus,
        modalShow: state => state.modal.modalShow,
        twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    },
}
</script>