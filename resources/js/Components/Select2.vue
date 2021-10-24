<template>
    <select class="form-control">
        <slot/>
    </select>
</template>

<script>
export default {
    props: ["value"],
    mounted: function() {
        var vm = this;
        $(this.$el)
        // init select2
        .select2({
            minimumResultsForSearch: Infinity
        })
        .val(this.value)
        .trigger("change")
        // emit event on change.
        .on("change", function() {
            vm.$emit("input", this.value);
        });
    },
    watch: {
        value: function(value) {
        // update value
        $(this.$el)
            .val(value)
            .trigger("change");
        }
    },
    destroyed: function() {
        $(this.$el)
        .off()
        .select2("destroy");
    }
}
</script>