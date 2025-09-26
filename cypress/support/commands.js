Cypress.Commands.add("labelled", text => {
    return cy.contains("label", text).invoke("attr", "for").then(id => cy.get(`#${id}`));
});
