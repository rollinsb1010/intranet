class CreateProductsProducts < ActiveRecord::Migration

  def up
    create_table :refinery_products do |t|
      t.string :name
      t.hstore :authors
      t.hstore :categories
      t.hstore :rel_categories
      t.hstore :assigned_to
      t.text :title
      t.textactive :title2
      t.string :sales_id
      t.string :online_id
      t.string :price
      t.integer :weight
      t.string :year_published
      t.string :type
      t.string :media
      t.text :description
      t.text :meta_desc
      t.text :meta_keywords
      t.integer :pages
      t.string :runtime
      t.string :language
      t.boolean :sedl_program
      t.text :testimonial
      t.text :citation
      t.boolean :update_content
      t.string :last_updated_by
      t.integer :position

      t.timestamps
    end

  end

  def down
    if defined?(::Refinery::UserPlugin)
      ::Refinery::UserPlugin.destroy_all({:name => "refinerycms-products"})
    end

    if defined?(::Refinery::Page)
      ::Refinery::Page.delete_all({:link_url => "/products/products"})
    end

    drop_table :refinery_products

  end

end
