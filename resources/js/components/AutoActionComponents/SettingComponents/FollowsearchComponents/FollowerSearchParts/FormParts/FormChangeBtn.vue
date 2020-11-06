<template>
    <div class="p-search__btn">
        <span class="p-search__btn__item" 
        @click="changeForm('normal')" 
        v-bind:class="{ js_search__btn__color: active.normal }">
        必須ワード
        </span>
        <span class="p-search__btn__item" 
        @click="changeForm('or')"
        v-bind:class="{ js_search__btn__color: active.or }">ORワード</span>
        <span class="p-search__btn__item" 
        @click="changeForm('ng')"
        v-bind:class="{ js_search__btn__color: active.ng }">NGワード</span>
    </div>
</template>
<script>
export default {
    data(){
        return{
            active:{
                normal:null,    
                or:null,
                ng:null,
            }
        }
    },
    methods:{
        //formを変える。
        changeForm(data){
            this.$store.commit('autoActionSetting/currentForm',data)
        },
        //変更したところの背景色を変える。
        changeActiveClass(data){
            for(let key in this.active) {
                if(key == data){
                    this.active[key] = true
                }
                else{
                    this.active[key] = false
                }
            }
        }
    },        
    computed:{
        getForm:function(){
            return this.$store.state.autoActionSetting.currentForm
        }
    },        
    watch:{
        //現在どのformを開いているかをチェックする。
        getForm(){
            if(this.getForm === 'normal'){
                this.changeActiveClass('normal')
            }else if(this.getForm === 'or'){
                this.changeActiveClass('or')
            }else if(this.getForm === 'ng'){
                this.changeActiveClass('ng')
            }
        }
    },
    created(){
        this.active.normal = true
        this.$store.commit('autoActionSetting/currentForm','normal')
    }
}
</script>