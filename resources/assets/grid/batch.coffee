# Grid 批处理

"use strict";

lego = require('../lego.coffee')
lego.createGridBatch = (gridContainerId, ids, batches) ->
    new Vue
        el: "##{gridContainerId}",

        data:
            ids: ids,
            selectedIds: []
            batches: batches,
            currentBatchAction: null,

        computed:
            selected: ->
                return @selectedIds.length;
            selectedIdsValue: ->
                return @selectedIds.join(',')

        methods:
            selectAll: ->
                @selectedIds = @ids

            trigger: (id)->
                if id not in @ids
                    return

                if id not in @selectedIds
                    @selectedIds.push id
                else
                    @selectedIds = (_id for _id in @selectedIds when _id != id)

            selectReverse: ->
                copy = @selectedIds
                @selectedIds = []
                for id in @ids
                    if id not in copy
                        @selectedIds.push id

            submitBatch: (action) ->
                @currentBatchAction = action;
