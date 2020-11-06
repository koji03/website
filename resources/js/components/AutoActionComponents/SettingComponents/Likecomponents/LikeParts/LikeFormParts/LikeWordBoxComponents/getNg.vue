<template>
<div>
    <p>キーワードが含まれているツイートを対象外にします。</p>
    <ul class="p-search__box" >
        <li class="p-search__list" v-if="ngList">
            <template v-for="item in ngList">
                <span class="p-search__list__item" :key="item.index">
                    {{item}}
                    <span>
                        <deleteBtn 
                        v-bind:word="item"
                        v-bind:type="'ng'"
                        />
                    </span>
                </span>
            </template>
        </li>
    </ul>
</div>

</template>
<script>
import deleteBtn from './deleteBtn'
export default {
    components:{
        deleteBtn
    },
    computed:{
        ngList () {
           return this.$store.getters['autoActionSetting/getLikeNg']
      },
    }
}
</script>