describe("Sylius Admin Panel", () => {
    it("should create, view, edit and delete product variant attributes", () => {
        // Login
        cy.visit("/admin");
        cy.labelled("Username").type("admin@example.com");
        cy.labelled("Password").type("qwerty");
        cy.contains("Login").click();
        cy.contains("Dashboard");

        // Open variants attributes
        cy.contains("Variants attributes").click();
        cy.location("pathname").should("eq", "/admin/product-variant-attributes/");
        cy.contains("There are no results to display");

        // Create
        cy.contains("Create").click();
        cy.contains("Textarea").click();
        cy.location("pathname").should("eq", "/admin/product-variant-attributes/textarea/new");
        cy.contains("New variant attribute");
        cy.labelled("Code").type("test_textarea");
        cy.labelled("Name").type("Test Textarea");
        cy.contains("Create").click();

        // Edit
        cy.location("pathname").should("match", /^\/admin\/product-variant-attributes\/\d+\/edit/);
        cy.contains("Product variant attribute has been successfully created.");
        cy.labelled("Code").should("have.value", "test_textarea");
        cy.labelled("Name").should("have.value", "Test Textarea");
        cy.labelled("Name").clear().type("Test Textarea (Edited)");
        cy.contains("Save changes").click();

        // Index view
        cy.contains("Variants attributes").click();
        cy.contains("test_textarea");
        cy.contains("Test Textarea (Edited)");
        cy.contains("a", "Edit").click();
        cy.location("pathname").should("match", /^\/admin\/product-variant-attributes\/\d+\/edit/);

        // Delete
        cy.contains("Variants attributes").click();
        cy.get("table").contains("Delete").click();
        cy.contains("Confirm your action").parents(".modal").contains("Yes").click();
        cy.contains("There are no results to display");
    });
});
