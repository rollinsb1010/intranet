class ApplicationController < ActionController::Base
  # Prevent CSRF attacks by raising an exception.
  # For APIs, you may want to use :null_session instead.
  protect_from_forgery with: :exception

  #Escape doesn't work
  # def perl
  #   perl_cmd = Escape.shell_command(['perl', "#{RAILS_ROOT}/cgi-bin/index.cgi"]).to_S
  #   system perl_cmd
  #   return
  # end

end
