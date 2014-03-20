module Refinery
  module Products
    class Product < Refinery::Core::BaseModel
      self.table_name = 'refinery_products'


      validates :name, :presence => true, :uniqueness => true
    end
  end
end
