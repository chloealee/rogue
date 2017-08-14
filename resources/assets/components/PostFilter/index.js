import React from 'react';

class PostFilter extends React.Component {
  constructor() {
    super();

    this.change = this.change.bind(this);
  }

  change(event) {
    this.props.onChange(event.target.value);
  }

  render() {
    return (
      <div className="container">
        <div className="container__block -third">
          <h4>Post Filter</h4>
          <div className="select">
              <select onChange={(event) => this.change(event)}>
                  <option>Accepted</option>
                  <option>Pending</option>
                  <option>Good Photo</option>
                  <option>Good Quote</option>
                  <option>Hide In Gallery 👻</option>
                  <option>Good For Sponsor</option>
                  <option>Good For Storytelling</option>
              </select>
          </div>
        </div>
      </div>
    )
  }
}

export default PostFilter;
