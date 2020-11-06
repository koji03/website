<template>
    <div class="l-footer">
        <div class="p-footer">
            <span>© {{ siteTitle }}, Inc.</span>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
    computed: {
        ...mapState({
            siteTitle: state => state.twitter.siteTitle,
        }),
    },   
}
</script>