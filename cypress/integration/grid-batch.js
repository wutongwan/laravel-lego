describe('Grid Batch Test', () => {
    it('测试点击多选按钮显示操作区域', () => {
        cy.visit('/grid-batch')
        // 默认情况下不显示
        cy.get('button.lego-disable-batch').should('not.be.visible')
        cy.get('button').contains('反选').should('not.be.visible')
        cy.get('button').contains('已选').should('not.be.visible')
        cy.get('button').contains('一键删除').should('not.be.visible')
        cy.get('button').contains('修改状态').should('not.be.visible')
        // 点击多选按钮
        cy.get('button').contains('多选')
            .should('have.class', 'lego-enable-batch')
            .click()
            .should(() => {
                expect(localStorage.getItem('Lego:Grid:BatchSwitcher:/grid-batch'))
                    .to.be.not.null
            })
        cy.get('button.lego-disable-batch').should('be.visible')
        cy.get('button').contains('全选').should('be.visible')
        cy.get('button.lego-disable-batch').should('be.visible')
        cy.get('button').contains('反选').should('be.visible')
        cy.get('button').contains('已选').should('be.visible')
        cy.get('button').contains('一键删除').should('be.visible')
        cy.get('button').contains('修改状态').should('be.visible')
        cy.get('input[type=checkbox].lego-batch-checkbox').should('be.visible')
        // 点击禁用按钮
        cy.get('button.lego-disable-batch').click()
            .should(() => {
                expect(localStorage.getItem('Lego:Grid:BatchSwitcher:/grid-batch'))
                    .to.be.null
            })
        cy.get('button.lego-disable-batch').should('not.be.visible')
        cy.get('button').contains('反选').should('not.be.visible')
        cy.get('button').contains('已选').should('not.be.visible')
        cy.get('button').contains('一键删除').should('not.be.visible')
        cy.get('button').contains('修改状态').should('not.be.visible')
        cy.get('input[type=checkbox].lego-batch-checkbox').should('not.be.visible')
    })

    it('测试 localStorage 有值时直接显示操作区域', () => {
        localStorage.setItem('Lego:Grid:BatchSwitcher:/grid-batch', Date.now())

        cy.visit('/grid-batch')
        cy.get('button.lego-disable-batch').should('be.visible')
        cy.get('button').contains('全选').should('be.visible')
    })

    it('测试选中', () => {
        localStorage.setItem('Lego:Grid:BatchSwitcher:/grid-batch', Date.now())
        cy.visit('/grid-batch')
        cy.get('button').contains('全选').click()
        cy.get('button[disabled]').should('contain.text', '已选 100 项')
        cy.get('button').contains('反选').click()
        cy.get('button[disabled]').should('contain.text', '已选 0 项')
    })

    it('测试选中+处理+浏览器后退选中状态不丢失', () => {
        localStorage.setItem('Lego:Grid:BatchSwitcher:/grid-batch', Date.now())
        cy.visit('/grid-batch')
        cy.get('button').contains('全选').click()
        cy.get('button[disabled]').should('contain.text', '已选 100 项')
        cy.get('button').contains('房型汇总').click()
        cy.get('a').contains('返回上一页面').click()
        cy.get('button[disabled]').should('contain.text', '已选 100 项') // 返回后选中状态不能丢失
    })

})
