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
                this.selectedIds = this.ids

            selectReverse: ->
                copy = @selectedIds
                @selectedIds = []
                for id in @ids
                    if id not in copy
                        @selectedIds.push id

            submitBatch: (action) ->
                @currentBatchAction = action;
                console.log $("##{gridContainerId}").find('form')
