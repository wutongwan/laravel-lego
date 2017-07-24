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
            currentBatchFormTarget: null,

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

            submitBatch: (batch) ->
                @currentBatchAction = batch.url;
                if typeof(batch.open_target) == "object"
                    windowName = "Lego_Popup_Window_Batch_#{batch.name}"
                    window.open(
                        'about:blank',
                        windowName,
                        "width=#{batch.open_target.width},height=#{batch.open_target.height},resizeable=no"
                    )
                    @currentBatchFormTarget = windowName
                else
                    console.log batch.open_target
                    @currentBatchFormTarget = batch.open_target
