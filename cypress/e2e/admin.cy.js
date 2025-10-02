describe("Sylius Admin Panel", () => {
    beforeEach(() => {
        // Login
        cy.viewport(1200, 600);
        cy.visit("/admin");
        cy.labelled("Username").type("sylius@example.com");
        cy.labelled("Password").type("sylius");
        cy.contains("button", "Login").click();
        cy.contains("Dashboard");

        // Create
        cy.contains("Variants attributes").click();
        cy.location("pathname").should("eq", "/admin/product-variant-attributes/");
        cy.contains("No results found");
        cy.contains("Create").click();
        cy.contains("Textarea").click();
        cy.location("pathname").should("eq", "/admin/product-variant-attributes/textarea/new");
        cy.contains("New Variant attribute");
        cy.labelled("Code").type("test_textarea");
        cy.labelled("Name").type("Test Textarea");
        cy.contains("Create").click();
        cy.contains("Product variant attribute has been successfully created");
        cy.location("pathname").should("match", /^\/admin\/product-variant-attributes\/\d+\/edit/);
    });

    afterEach(() => {
        // Delete
        cy.visit("/admin/product-variant-attributes/");
        cy.get("[data-bs-title = 'Delete'] button").click();
        cy.get(".modal.fade.show").contains("Are you sure you want to perform this action?").parents(".modal").contains("Delete").click();
        cy.contains("No results found");
    });

    it("should create, view, edit and delete product variant attributes", () => {
        // Edit
        cy.labelled("Code").should("have.value", "test_textarea");
        cy.labelled("Name").should("have.value", "Test Textarea");
        cy.labelled("Name").clear().type("Test Textarea (Edited)");
        cy.contains("Update").click();
        cy.contains("Product variant attribute has been successfully updated");

        // Index view
        cy.contains("Variants attributes").click();
        cy.contains("test_textarea");
        cy.contains("Test Textarea (Edited)");
        cy.get("table a[data-bs-title = \"Edit\"]").click();
        cy.location("pathname").should("match", /^\/admin\/product-variant-attributes\/\d+\/edit/);
    });

    // TODO
    it.skip("should create, view, edit and delete product variant attribute values", () => {
        // Add
        cy.contains("Products").click();
        cy.contains("000F_office_grey_jeans").parents("tr").within($tr => {
            cy.wrap($tr).contains("Manage variants").click();
            cy.wrap($tr).contains("List variants").click();
        });
        cy.contains("000F_office_grey_jeans-variant-0").parents("tr").contains("Edit").click();
        cy.contains("Attributes").click();
        cy.get("[id = \"umanit_product_variant_attribute_choice\"]").parents(".dropdown").click();
        cy.contains("div", "Test Textarea").click();
        cy.contains("Add attributes").click();
        cy.contains("label", "Test Textarea").parents(".attribute").get("textarea").first().type("Hello, World!");
        cy.contains("Save changes").click();
        cy.location("pathname").should("match", /^\/admin\/products\/\d+\/variants/);
        cy.contains("Product variant has been successfully updated");

        // Edit
        cy.contains("000F_office_grey_jeans-variant-0").parents("tr").contains("Edit").click();
        cy.contains("Attributes").click();
        cy.contains("Test Textarea");
        cy.contains("Hello, World!").clear().type("Hello, World! (Edited)");
        cy.contains("Save changes").click();
        cy.location("pathname").should("match", /^\/admin\/products\/\d+\/variants/);
        cy.contains("Product variant has been successfully updated");

        cy.contains("000F_office_grey_jeans-variant-0").parents("tr").contains("Edit").click();
        cy.contains("Attributes").click();
        cy.contains("Hello, World! (Edited)");
    });
});
